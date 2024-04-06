<?php
/**
 * abavo_search - ConfigurationExample.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 07.06.2018 - 15:59:41
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use GeorgRinger\News\Domain\Repository\NewsRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\Domain\Api;

/**
 * ConfigurationExample
 * 
 * Shows how does a ApiConfiguration work
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ConfigurationExample implements ConfigurationInterface
{

    /**
     * get config method
     * 
     * @return array Based on \TYPO3\CMS\Extbase\Mvc\View\JsonView->configuration
     */
    public static function getJsonViewConfiguration()
    {
        return [
            '_only' => ['uid', 'importId', 'sysLanguageUid', 'feGroup', 'title', 'teaser', 'bodytext', 'alternativeTitle', 'keywords', 'autor', 'datetime', 'categories', 'media', 'relatedFiles'],
            '_descend' => [
                'datetime' => [],
                'categories' => [
                    '_descendAll' => [
                        '_only' => ['uid', 'title'],
                    ]
                ],
                'media' => [
                    '_descendAll' => [
                        '_only' => ['uid', 'fileUid', 'title'],
                    ]
                ],
                'relatedFiles' => [
                    '_descendAll' => [
                        '_only' => ['uid', 'fileUid', 'title'],
                    ]
                ],
            ]
        ];
    }

    /**
     * get the backend label
     * 
     * @return string
     */
    public static function getLabel()
    {
        return 'Example API Indexer Config (based on tx_news records)';
    }

    /**
     * get the repositoryBackend
     * @return RepositoryBackend
     */
    public static function getRepositoryBackend()
    {
        return GeneralUtility::makeInstance(
                RepositoryBackend::class, NewsRepository::class, 'findAll', $argumentsArr = null, ModifyItemExample::class
        );
    }

    /**
     * get the IndexConfiguration
     * @return IndexConfiguration
     */
    public static function getIndexConfiguration()
    {
        // Index-Field configurations
        $titleConf    = ['fields' => ['title']];
        $contentConf  = ['fields' => ['teaser', 'bodytext'], 'separator' => PHP_EOL];
        $abstractConf = ['fields' => ['alternativeTitle', 'keywords', 'autor'], 'separator' => PHP_EOL, 'langfile' => 'EXT:news/Resources/Private/Language/locallang_db.xlf'];

        /*  $params will be parced and placeholders {FIELD_<YOURFIELDUPPERCASE>} replaced
         *  NOTE: "importId" in params is the workaround; use localizedUid in your project if possible!
         */
        $params = '&L={FIELD_SYSLANGUAGEUID}&tx_news_pi1[controller]=News&tx_news_pi1[action]=detail&tx_news_pi1[news]={FIELD_IMPORTID}';

        // NOTE: "importId" is the workaround to get the _localizedUid; Normaly just create a getter for _localizedUid to access of JSON
        return GeneralUtility::makeInstance(
                IndexConfiguration::class, $titleConf, $contentConf, $abstractConf, $params, $identifier = 'importId', $resultTemplatePartial = IndexConfiguration::RESULT_TEMPLATE_PARTIAL_DEFAULT
        );
    }
}