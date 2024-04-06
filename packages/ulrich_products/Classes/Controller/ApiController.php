<?php
/**
 * ulrich_products - ApiController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 24.05.2018 - 08:20:03
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Controller;

use Psr\Http\Message\ResponseInterface;
use Abavo\UlrichProducts\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use Abavo\UlrichProducts\Domain\Model\Product;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use Abavo\UlrichProducts\Domain\Model\Product\ApiQuery;

/**
 * ApiController
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ApiController extends ActionController
{
    public const STATE_NOTICE  = -2;
    public const STATE_INFO    = -1;
    public const STATE_OK      = 0;
    public const STATE_WARNING = 1;
    public const STATE_ERROR   = 2;

    /**
     * productRepository
     *
     * @var ProductRepository
     */
    protected $productRepository = null;

    /**
     * initialize productsAction
     */
    protected function initializeProductsAction()
    {
        parent::initializeAction();

        $propertyMappingConfiguration = $this->arguments['query']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowAllProperties();
        $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class, PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
    }

    /**
     * action products
     *
     * @param ApiQuery $query The api request
     * @return void
     */
    public function productsAction(ApiQuery $query): ResponseInterface
    {
        $this->variables = ['data' => ['items' => [], 'showMoreLink' => false], 'message' => '', 'status' => null];

        try {
            // Set JsonView configuration
            $this->view->setConfiguration([
                'data' => [
                    'items' => [
                        '_descendAll' => [
                            '_only' => GeneralUtility::trimExplode(',', $query->getProductProperties()),
                            '_descend' => [
                                'categories' => [
                                    '_descendAll' => [
                                        '_only' => ['uid', 'localizedUid', 'title', 'description'],
                                    ]
                                ],
                                'originCountry' => [
                                    '_only' => ['uid', 'nameLocalized', 'isoCodeA2'],
                                ],
                                'media' => [
                                    '_descendAll' => [
                                        '_exclude' => ['pid']
                                    ]
                                ],
                                'contact' => [
                                    '_only' => ['name', 'email', 'phone'],
                                ],
                            ]
                        ]
                    ]
                ]
            ]);


            // Set cacheIdentifier
            $cacheIdentifier = $this->makeCacheIdentifier(implode('|', $query->__toArray()));

            // Get CacheInstance
            if ($this->cacheInstance->has($cacheIdentifier)) {
                // Cache exist, get $variables
                $this->variables['data'] = $this->cacheInstance->get($cacheIdentifier);
            } else {
                // Generate product items
                if ($products = $this->productRepository->findForApiQuery($query)) {

                    // Get allcount
                    $allQuery = clone $query;
                    $allCount = ($query->getCategory()) ? $this->productRepository->findForApiQuery($allQuery->setOffset(0)->setLimit(0))->count() : $this->productRepository->countAll();

                    // Create result
                    $items = $products->toArray();
                    array_walk($items,
                        function($product) use (&$items, $query) {
                        if ($product instanceof Product) {
                            // Generate categories
                            $category = $query->getCategory() ?: $product->getCategories()->current();
                            $product->setUri($this->uriBuilder->uriFor(
                                    'show',
                                    [
                                    'category' => ($category) ? $category->getLocalizedUid() : 0,
                                    'product' => $product->getUid(),
                                    /*
                                      'query' => [
                                      'char' => $query->getChar(),
                                      'category' => ($query->getCategory()) ? $query->getCategory()->getLocalizedUid() : 0,
                                      'limit' => ($query->getOffset()) ? $query->getOffset() : $query->getLimit(),
                                      'offset' => 0
                                      ] */
                                    ], 'Product'
                                )
                            );
                        }
                    });

                    $this->variables['data']['items']        = $items;
                    $this->variables['data']['showMoreLink'] = ($query->getOffset() + $products->count() < $allCount);

                    // Safe variables in cache
                    //$this->cacheInstance->set($cacheIdentifier, $this->variables['data'], [$this->actionMethodName]);
                }
            }

            $this->variables['status'] = self::STATE_OK;
            //
        } catch (\Exception $ex) {
            $this->variables['message'] = $ex->getMessage();
            $this->variables['status']  = self::STATE_ERROR;
            $this->response->setStatus(500);
        }

        // Assign data to view
        $this->view->setVariablesToRender(array_keys($this->variables));
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }

    /**
     * initialize media action
     */
    public function initializeMediaAction()
    {
        parent::initializeAction();

        if ($this->request->hasArgument('mode') && $this->request->getArgument('mode') === 'json') {
            $this->defaultViewObjectName = JsonView::class;
        }
    }

    /**
     * media action
     *
     * examples:
     * <img src="{f:uri.action(pluginName:'api', pageType:settings.pageTypeApi.media, arguments:'{sysFileReference:30, params:{width:200}}')}" /><br>
     * <f:link.action pluginName="api" pageType="{settings.pageTypeApi.media}" arguments="{sysFileReference:30, params:{width:200}, mode:'json'}">JSON</f:link.action>
     *
     * @param FileReference $sysFileReference
     * @param array $params the image params
     * @param string $mode the return mode
     * @throws StopActionException
     */
    public function mediaAction(FileReference $sysFileReference, $params = [], $mode = 'image'): ResponseInterface
    {
        $originalResource = $sysFileReference->getOriginalResource();
        $imageArr         = GeneralUtility::makeInstance(ContentObjectRenderer::class)->getImgResource($originalResource, $params);

        if (isset($imageArr['processedFile']) && $imageArr['processedFile'] instanceof ProcessedFile) {

            switch ($mode) {
                case 'json':
                    $this->variables = [
                        'width' => $imageArr[0],
                        'height' => $imageArr[1],
                        'uri' => $imageArr['processedFile']->getPublicUrl()
                    ];
                    $this->view->setVariablesToRender(array_keys($this->variables));
                    $this->view->assignMultiple($this->variables);
                    break;

                case 'image':
                default:
                    $this->controllerContext->getResponse()->setHeader('Content-Type', $originalResource->getMimeType());
                    $this->controllerContext->getResponse()->setHeader('Content-Disposition', 'inline; filename="'.urlencode($originalResource->getName()).'"');
                    $this->controllerContext->getResponse()->setHeader('Content-Transfer-Encoding', 'binary');
                    $this->controllerContext->getResponse()->setHeader('Accept-Ranges', 'bytes');
                    echo $imageArr['processedFile']->getContents();
                    throw new StopActionException;
            }
        }
        return $this->htmlResponse();
    }

    public function injectProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }
}