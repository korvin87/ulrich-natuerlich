<?php
/**
 * MI-Media-Manager (EXT:site_package) - UtilityHelper.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.11.2017 - 08:01:07
 * 
 * @copyright: since 2017 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\SitePackage\User;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
/**
 * UserFunc to use the GeneralUtility
 * 
 * TypoScript example:
 *   cObject = USER
 *   cObject {
 *       userFunc = Abavo\SitePackage\User\UtilityHelper->generalUtility
 *       method = _GET
 *       param = tx_myextension_pi
 *       arrayKey = title
 *   }
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class UtilityHelper
{
    /**
     * Reference to the parent (calling) cObject set from TypoScript
     */
    protected $cObj;

    /**
     * @param   string   Empty string (no content to process)
     * @param   array    TypoScript configuration
     * @return  mixed    result
     */
    public function generalUtility($content = '', $conf = [])
    {
        $result = call_user_func('\TYPO3\CMS\Core\Utility\GeneralUtility::'.$conf['method'], $conf['param']);

        if (isset($conf['arrayKey']) && isset($result[$conf['arrayKey']])) {
            $result = $result[$conf['arrayKey']];
        }

        return $result;
    }

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }
}