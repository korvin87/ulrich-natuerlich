<?php
/**
 * abavo_search - ConfigurationInterface.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 07.06.2018 - 16:00:12
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

/**
 * ConfigurationInterface
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
interface ConfigurationInterface
{

    /**
     * @return array Based on \TYPO3\CMS\Extbase\Mvc\View\JsonView->configuration
     */
    public static function getJsonViewConfiguration();

    /**
     * @return string the label
     */
    public static function getLabel();

    /**
     * @return RepositoryBackend
     */
    public static function getRepositoryBackend();

    /**
     * @return IndexConfiguration
     */
    public static function getIndexConfiguration();
}