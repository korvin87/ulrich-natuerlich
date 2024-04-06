<?php
/*
 * abavo_search
 *
 * @copyright   2017 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to render Typolinks
 * usage:
 * {namespace vh=Abavo\AbavoContentelements\ViewHelpers}
 * <vh:Typolink parameter="{address.url}">{address.url}</vh:Typolink>
 * @author mbruckmoser
 */
class TypolinkViewHelper extends AbstractViewHelper
{
    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * contentObjectRenderer
     *
     * @var ContentObjectRenderer
     */
    protected $cObject = null;

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('parameter', 'string', '', false, '');
        $this->registerArgument('target', 'string', '', false, '');
        $this->registerArgument('noCache', 'boolean', '', false, false);
        $this->registerArgument('useCacheHash', 'boolean', '', false, true);
        $this->registerArgument('additionalParams', 'array', '', false, []);
        $this->registerArgument('ATagParams', 'string', '', false, '');
        $this->registerArgument('highlightParam', 'string', '', false, '');
        $this->registerArgument('returnLast', 'string', '', false, '');
        $this->registerArgument('forceAbsoluteUrl', 'boolean', '', false, false);
        $this->registerArgument('useArrayKeyAsParam', 'boolean', '', false, false);
        $this->registerArgument('forceAdditionalSettings', 'boolean', '', false, false);
    }

    /**
     * Render method
     * 
     * @return mixed
     */
    public function render()
    {
        //default
        $typoLinkConf = [
            'parameter' => $this->arguments['parameter'],
            'no_cache' => (boolean) $this->arguments['noCache'],
            'useCacheHash' => (boolean) $this->arguments['useCacheHash'],
            'forceAbsoluteUrl' => (boolean) $this->arguments['forceAbsoluteUrl'],
            'ATagParams' => $this->arguments['ATagParams'],
            'returnLast' => $this->arguments['returnLast'],
            'target' => $this->arguments['returnLast']
        ];


        // additional params
        if (is_countable($this->arguments['additionalParams']) ? count($this->arguments['additionalParams']) : 0) {
            if ($this->arguments['useArrayKeyAsParam']) {
                $typoLinkConf['additionalParams'] = GeneralUtility::implodeArrayForUrl('', $this->arguments['additionalParams']);
            } else {
                $typoLinkConf['additionalParams'] = implode('', $this->arguments['additionalParams']);
            }
        }

        // Highlighting
        if (strlen($this->arguments['highlightParam'])) {
            $highlightParams = GeneralUtility::trimExplode(' ', $this->arguments['highlightParam'], true);
            if ((boolean) count($highlightParams)) {

                if (array_key_exists('tx_abavosearch.', $GLOBALS['TSFE']->config['config'])) {

                    $extConf = $GLOBALS['TSFE']->config['config']['tx_abavosearch.']['contentModifier.'];
                    if ((boolean) $extConf['enable'] === true) {
                        foreach ($highlightParams as $key => $param) {
                            $typoLinkConf['additionalParams'] .= ((strpos($this->arguments['parameter'], '?') !== false) ? '&' : '?' ).trim($extConf['param']).'['.(int) $key.']='.$param;
                        }
                    }
                }
            }
        }

        // Generate link
        $linkText = $this->renderChildren();
        $link     = $this->cObject->cObjGetSingle('TEXT', ['typolink.' => $typoLinkConf, 'value' => $linkText]);

        /*
         * Workaround forcing external url with settings (additionalParams + target)
         * This is because additionalParams will lost after building a external URL; We need this for integrating external resources to internal site;
         */
        if ($this->arguments['forceAdditionalSettings'] === true && isset($typoLinkConf['additionalParams']) && strpos($link, $typoLinkConf['additionalParams']) === false && $typoLinkConf['useCacheHash'] === false) {

            $typoLink         = $this->cObject->typoLink_URL($typoLinkConf);
            $typoLinkCleanArr = GeneralUtility::trimExplode('&cHash=', $typoLink, true);

            if (!empty($typoLinkCleanArr)) {
                $typoCleanLink = current($typoLinkCleanArr);
                $link          = '<a href="'.$typoCleanLink.$typoLinkConf['additionalParams'].'" target="'.$typoLinkConf['target'].'">'.$linkText.'</a>';
            }
        }

        // Return
        return $link;
    }

    public function injectCObject(ContentObjectRenderer $cObject): void
    {
        $this->cObject = $cObject;
    }
}