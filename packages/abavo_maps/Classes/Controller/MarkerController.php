<?php

namespace TYPO3\AbavoMaps\Controller;

/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\AbavoMaps\Domain\Repository\MarkerRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MarkerController
 */
class MarkerController extends ActionController
{
    /**
     * markerRepository
     *
     * @var MarkerRepository
     */
    protected $markerRepository;

    /**
     * action update
     *
     * @return void
     */
    public function updateAction(): ResponseInterface
    {

        /* Because backward compatibility to other Extensions, we dont change the request and work with a Dummy "PiEid"
         *  ADVICE: change request in abavo_address, abavo_tt_address and use here $this->request->arguments again */
        $arrAguments = GeneralUtility::_GP('request')['arguments'];
        $repo        = GeneralUtility::removeXSS(base64_decode($arrAguments['repo']));

        // Init given Repository and get marker object from request
        $objManager = GeneralUtility::makeInstance(ObjectManager::class);

        if (is_object($objRepository = $objManager->get($repo))) {

            $object = $objRepository->findByUkey(GeneralUtility::removeXSS($arrAguments['ukey']));
            if ($object <> null) {
                $object->setLatitude(GeneralUtility::removeXSS($arrAguments['lat']));
                $object->setLongitude(GeneralUtility::removeXSS($arrAguments['lng']));
                $objRepository->update($object);

                $objManager->get(PersistenceManagerInterface::class)->persistAll();
                $this->addFlashMessage(GeneralUtility::removeXSS($arrAguments['ukey']), 'Update successed', AbstractMessage::OK);
            } else {
                $this->addFlashMessage(GeneralUtility::removeXSS($arrAguments['ukey']).' not found.', 'Update failed', AbstractMessage::INFO);
            }
        } else {
            $this->addFlashMessage('No valid repository defined.', 'Update failed', AbstractMessage::ERROR);
        }
        return $this->htmlResponse();
    }

    public function injectMarkerRepository(MarkerRepository $markerRepository): void
    {
        $this->markerRepository = $markerRepository;
    }
}