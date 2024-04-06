<?php

namespace Abavo\SitePackage\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/*
 * site_package
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * BackendController
 *
 * @author mbruckmoser
 */
class BackendController extends ActionController
{
    /**
     * Core Registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * action Administration
     */
    public function adminAction(): ResponseInterface
    {
        $registeredDocumentation = '';

        // Get Documentation
        if ($this->request->hasArgument('registerDocumentation')) {
            $registerDocumentation = $this->request->getArgument('registerDocumentation');

            // Save to sys_registry
            $this->coreRegistry->set('Abavo.SitePackage', 'Documentation', $registerDocumentation);
            $this->addFlashMessage('Documentation seems to successful registered.', 'Documentation register', AbstractMessage::OK);
        }

        // Get from sys_registry
        $registeredDocumentation = $this->coreRegistry->get('Abavo.SitePackage', 'Documentation');

        // Assign data to view
        $this->view->assignMultiple([
            'registeredDocumentation' => $registeredDocumentation
        ]);
        return $this->htmlResponse();
    }

    /**
     * action manual
     *
     * @return void
     */
    public function manualAction()
    {
        $registeredDocumentation = $this->coreRegistry->get('Abavo.SitePackage', 'Documentation');
        $file = null;

        if ($registeredDocumentation !== '') {
            $file = GeneralUtility::getFileAbsFileName($registeredDocumentation);
        }
        if (file_exists($file)) {

            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($file) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            readfile($file);
        } else {
            echo 'NO MANUAL FOUND:' . $file;
        }

        exit();
    }

    public function injectCoreRegistry(Registry $coreRegistry): void
    {
        $this->coreRegistry = $coreRegistry;
    }
}
