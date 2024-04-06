<?php
/**
 * abavo_search - IndexConfiguration.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.06.2018 - 13:22:28
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use \TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * IndexConfiguration
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class IndexConfiguration
{
    public const CONFIGURATION_TEMPATE           = ['fields' => [], 'separator' => ': ', 'langfile' => null];
    public const RESULT_TEMPLATE_PARTIAL_DEFAULT = 'Index/Api';

    /**
     * @var string
     */
    protected $identifier = 'uid';

    /**
     * @var array
     */
    protected $title = self::CONFIGURATION_TEMPATE;

    /**
     * @var array
     */
    protected $content = self::CONFIGURATION_TEMPATE;

    /**
     * @var array
     */
    protected $abstract = self::CONFIGURATION_TEMPATE;

    /**
     * @var string
     */
    protected $params = '';

    /**
     * @var string
     */
    protected $resultTemplatePartial = self::CONFIGURATION_TEMPATE;

    /**
     * the constuctor
     * 
     * @param array $title
     * @param array $content
     * @param array $abstract
     * @param string $params
     * @param string $identifier
     * @param string $resultTemplatePartial
     */
    public function __construct(array $title = null, array $content = null, array $abstract = null, string $params = null, string $identifer = null, string $resultTemplatePartial = null)
    {
        if (is_array($title)) {
            ArrayUtility::mergeRecursiveWithOverrule($this->title, $title);
        }
        if (is_array($content)) {
            ArrayUtility::mergeRecursiveWithOverrule($this->content, $content);
        }
        if (is_array($abstract)) {
            ArrayUtility::mergeRecursiveWithOverrule($this->abstract, $abstract);
        }
        if ($params) {
            $this->params = $params;
        }
        if ((boolean) $identifer) {
            $this->identifier = $identifer;
        }
        if ((boolean) $resultTemplatePartial) {
            $this->resultTemplatePartial = $resultTemplatePartial;
        }
    }

    /**
     * Get the identifer field
     * 
     * @return string
     */
    public function getIdentifer()
    {
        return $this->identifier;
    }

    /**
     * get the title configuration
     * 
     * @return array
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * get the content configuration
     * 
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * get the abstract configuration
     * 
     * @return array
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * get the params configuration
     * 
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * get the resultTemplatePartial
     * 
     * @return string
     */
    public function getResultTemplatePartial()
    {
        return $this->resultTemplatePartial;
    }
}