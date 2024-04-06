<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
/**
 * AjaxResponse helper
 */
class AjaxResponse implements SingletonInterface
{

    /**
     * getJsonView
     *
     * @param ControllerContext $controllerContext
     * @param array $results
     * @return JSON
     */
    public function getJsonView($controllerContext = null, $results = [])
    {

        $this->view                  = GeneralUtility::makeInstance(JsonView::class);
        $this->defaultViewObjectName = JsonView::class;
        ($controllerContext != null) ? $this->view->setControllerContext($controllerContext) : null;

        $this->view->assign('value', $results);

        return $this->view;
    }
}