<?php
/**
 * MI-Media-Manager (EXT:site_package) - GetCondition.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.11.2017 - 09:08:23
 * 
 * @copyright: since 2017 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\SitePackage\User;

use TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * GetCondition
 * 
 * based on https://docs.typo3.org/typo3cms/TyposcriptReference/Conditions/Reference/Index.html#id51
 * 
 * TypoScript condition example:
 * [Abavo\SitePackage\User\GetCondition isset, tx_myextension_pi, arrayParam]
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class TypoScriptGetCondition extends AbstractCondition
{
    /*
     * @var array the $_GET params filled by GeneralUtility
     */
    protected $getParams = [];

    /**
     * The Constructor
     */
    public function __construct()
    {
        $this->getParams = GeneralUtility::_GET();
    }

    /**
     * Evaluate condition
     *
     * @param array $conditionParameters
     * @return bool
     */
    public function matchCondition(array $conditionParameters)
    {
        $result = false;

        if (!empty($conditionParameters) && $conditionParameters[0] === 'isset') {
            if (count($conditionParameters) === 2) {
                $result = isset($this->getParams[$conditionParameters[1]]);
            }
            if (count($conditionParameters) === 3) {
                $result = isset($this->getParams[$conditionParameters[1]][$conditionParameters[2]]);
            }
        }

        return $result;
    }
}