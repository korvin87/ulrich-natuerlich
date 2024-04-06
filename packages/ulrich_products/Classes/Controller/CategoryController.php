<?php
/**
 * ulrich_products - CategoryController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 24.05.2018 - 08:24:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Controller;

use Psr\Http\Message\ResponseInterface;
use Abavo\UlrichProducts\Domain\Repository\CategoryRepository;
/**
 * CategoryController
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class CategoryController extends ActionController
{
    /**
     * categoryRepository
     *
     * @var CategoryRepository
     */
    protected $categoryRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction(): ResponseInterface
    {
        $this->variables['categories'] = $this->categoryRepository->findByTableAndUid('pages', $this->settings['pageData']['uid']);
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }

    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }
}