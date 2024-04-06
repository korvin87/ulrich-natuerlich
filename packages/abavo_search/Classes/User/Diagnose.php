<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Reports\Status;
use Abavo\AbavoSearch\Indexers\FalIndexer;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Diagnose Module
 *
 * @author mbruckmoser
 */
class Diagnose implements SingletonInterface
{
    protected $status = [];

    /**
     * Returns the status for this report
     *
     * @return array List of statuses
     */
    public function getStatus()
    {
        $this->status = [
            'uploadsFolder' => $this->getUploadsFolderStatus(),
        ];
        $this->status = array_merge($this->status, $this->getLogFileErrorStatus());

        foreach ($this->getServerModulsStatus() as $status) {
            array_push($this->status, $status);
        }
        foreach ($this->getShellScriptsStatus() as $status) {
            array_push($this->status, $status);
        }

        return $this->status;
    }

    /**
     * Simply shellScripts check
     *
     * @return Status
     */
    protected function getShellScriptsStatus()
    {
        $statuses = null;

        $shellScriptsOK = true;
        $shellScripts   = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName('EXT:abavo_search/Resources/Private/Scripts/'), 'sh', true);
        foreach ($shellScripts as $shellScript) {
            if (!is_executable($shellScript)) {
                $statuses[]     = GeneralUtility::makeInstance(Status::class, 'File permission', 'ShellScript not executable', $shellScript,
                        Status::ERROR);
                $shellScriptsOK = false;
            }
        }
        if ($shellScriptsOK) {
            $statuses[] = GeneralUtility::makeInstance(Status::class, 'File permission', '', 'ShellScripts has correct executable permissions',
                    Status::OK);
        }
        return $statuses;
    }

    /**
     * Server moduls check
     *
     * @return Status
     */
    protected function getServerModulsStatus()
    {
        $statuses               = [];
        $serverDefaultToolPaths = GeneralUtility::trimExplode(',', FalIndexer::SERVER_TOOL_PATHS);
        $serverModulesOK        = false;

        $serverModules = [
            'pdftotext' => 'PDF',
            'pdfinfo' => 'PDF',
            'catdoc' => 'DOC, DOCX',
            'catppt' => 'PPT, PPTX',
            'xls2csv' => 'XLS',
            'unzip' => 'XLSX, DOCX, PPTX'
        ];

        foreach ($serverModules as $module => $fileExtension) {

            foreach ($serverDefaultToolPaths as $path) {
                if (is_executable($path.$module) === true) {
                    $serverModulesOK = true;
                }
            }
            if (!$serverModulesOK) {
                $message    = 'Module not reachable: <i>'.$path.$module.'</i> for indexing file types <i>'.$fileExtension.'</i>. Please be sure to define in FALindexer configuration the toolPath or make it accesible.';
                $statuses[] = GeneralUtility::makeInstance(Status::class, 'Server modules', $path.$module, $message, Status::WARNING);
            }
        }


        if ($serverModulesOK) {
            $message    = 'The server modules ('.implode(', ', array_flip($serverModules)).') for file indexing are reachable.';
            $statuses[] = GeneralUtility::makeInstance(Status::class, 'Server modules', '', $message, Status::OK);
        }

        return $statuses;
    }

    /**
     * Is LOCK_FILE in uploads/tx_abavo_search/Lock/ writeable?
     *
     * @return Status
     */
    protected function getUploadsFolderStatus()
    {
        $status       = null;
        $lockFilePath = 'typo3temp/var/locks/';
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '9.5', '>=') && Environment::isComposerMode()) {
            $lockFilePath = Environment::getProjectPath().'/var/lock/';
        }

        if (!is_writable(GeneralUtility::getFileAbsFileName($lockFilePath))) {
            $message = "It is NOT possible to write the LOCK_FILE to the $lockFilePath directory.";
            $status  = GeneralUtility::makeInstance(Status::class, 'LOCK_FILE', '', $message, Status::ERROR);
        } else {
            $message = "It is possible to write the LOCK_FILE to the $lockFilePath directory.";
            $status  = GeneralUtility::makeInstance(Status::class, 'LOCK_FILE', '', $message, Status::OK);
        }
        return $status;
    }

    /**
     * Last Errors from logfile
     *
     * @return Status
     */
    protected function getLogFileErrorStatus()
    {
        $status    = null;
        $logFileOK = true;
        $logFiles  = [
            basename(current(current($GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoSearch']['Controller']['IndexCommandController']['writerConfiguration']))['logFile']),
            basename(current(current($GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoSearch']['Hooks']['ContentModifier']['writerConfiguration']))['logFile'])
        ];

        foreach ($logFiles as $logFileName) {

            $logFilePath = 'typo3temp/logs/';
            if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '9.5', '>=') && Environment::isComposerMode()) {
                $logFilePath = Environment::getProjectPath().'/var/log/';
            }
            $logFile = $logFilePath.$logFileName;
            if (file_exists($logFile)) {
                $handleLogFile = fopen($logFile, 'r');
                if (file_exists($logFile)) {
                    while (!feof($handleLogFile)) {
                        $parseLine = fgets($handleLogFile);
                        if ((boolean) strpos($parseLine, ' [ERROR] ')) {
                            $logFileOK = false;
                            break;
                        }
                    }
                    fclose($handleLogFile);
                }
            }

            if ($logFileOK) {
                $status[$logFileName] = GeneralUtility::makeInstance(Status::class, 'Error Log: '.$logFileName, 'No errors', $logFile, Status::OK);
            } else {
                $status[$logFileName] = GeneralUtility::makeInstance(Status::class, 'Error Log: '.$logFileName, 'One or more errors', $logFile,
                        Status::WARNING);
            }
        }

        return $status;
    }
}