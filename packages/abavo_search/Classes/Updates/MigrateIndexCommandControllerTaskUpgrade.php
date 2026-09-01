<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Abavo\AbavoSearch\Updates;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Scheduler\Execution;
use TYPO3\CMS\Scheduler\Task\ExecuteSchedulableCommandTask;

/**
 * Rewrites legacy `tx_scheduler_task` rows that still reference the removed
 * Extbase CommandController-based scheduler task
 * (`TYPO3\CMS\Extbase\Scheduler\Task` wrapping
 * `Abavo\AbavoSearch\Controller\IndexCommandController::updateCommand`) as
 * `ExecuteSchedulableCommandTask` rows for the new `abavo_search:update`
 * Symfony console command. Frequency, taskGroup, disabled flag and description
 * are preserved.
 */
class MigrateIndexCommandControllerTaskUpgrade implements UpgradeWizardInterface, ChattyInterface
{
    public const IDENTIFIER = 'abavoSearchMigrateIndexCommandControllerTask';

    private const OLD_TASK_CLASS = 'TYPO3\\CMS\\Extbase\\Scheduler\\Task';

    /**
     * Command identifiers Extbase used to auto-derive for
     * IndexCommandController::updateCommand. Extbase historically produced
     * "<extensionName>:<controller>:<method>" — we accept both the UpperCamel
     * extension name and the underscore extension key form.
     *
     * @var string[]
     */
    private const KNOWN_OLD_COMMAND_IDENTIFIERS = [
        'abavosearch:index:update',
        'abavo_search:index:update',
    ];

    private ?OutputInterface $output = null;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getTitle(): string
    {
        return 'abavo_search: migrate legacy IndexCommandController scheduler tasks';
    }

    public function getDescription(): string
    {
        return 'Rewrites scheduler tasks that used the removed Extbase CommandController'
            . ' (Abavo\\AbavoSearch\\Controller\\IndexCommandController::updateCommand) so'
            . ' they invoke the new abavo_search:update Symfony console command via'
            . ' TYPO3\\CMS\\Scheduler\\Task\\ExecuteSchedulableCommandTask. Frequency,'
            . ' next-execution time, task group, disabled flag and description are'
            . ' preserved.';
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    public function updateNecessary(): bool
    {
        return $this->findLegacyTasks() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_scheduler_task');

        $migrated = 0;
        $skipped  = 0;
        foreach ($this->findLegacyTasks() as $row) {
            $task = $this->buildReplacementTask((string)$row['serialized_task_object'], (int)$row['uid']);
            if ($task === null) {
                $skipped++;
                $this->write(sprintf('  * task uid=%d: could not extract storagePid — left untouched', $row['uid']));
                continue;
            }

            $connection->update(
                'tx_scheduler_task',
                ['serialized_task_object' => serialize($task)],
                ['uid' => (int)$row['uid']]
            );

            $migrated++;
            $args = $task->getArguments();
            $this->write(sprintf(
                '  * migrated task uid=%d (storagePid=%s)',
                $row['uid'],
                $args['storagePid'] ?? '?'
            ));
        }

        $this->write(sprintf(
            'Migrated %d task%s, skipped %d.',
            $migrated,
            $migrated === 1 ? '' : 's',
            $skipped
        ));

        return true;
    }

    /**
     * Rows that both reference the removed Extbase scheduler task class AND
     * carry one of the known command identifiers.
     *
     * @return array<int, array{uid:int, serialized_task_object:string}>
     */
    private function findLegacyTasks(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $qb = $connectionPool->getQueryBuilderForTable('tx_scheduler_task');
        $qb->getRestrictions()->removeAll();

        // tx_scheduler_task is small — filter in PHP instead of a LIKE pattern
        // containing backslashes (which MySQL LIKE would treat as its escape
        // character and silently strip).
        $rows = $qb
            ->select('uid', 'serialized_task_object')
            ->from('tx_scheduler_task')
            ->execute()
            ->fetchAllAssociative();

        return array_values(array_filter($rows, function (array $row): bool {
            $blob = (string)($row['serialized_task_object'] ?? '');
            if (strpos($blob, self::OLD_TASK_CLASS) === false) {
                return false;
            }
            $commandIdentifier = $this->extractStringProperty($blob, 'commandIdentifier');
            return $commandIdentifier !== null
                && in_array(strtolower($commandIdentifier), self::KNOWN_OLD_COMMAND_IDENTIFIERS, true);
        }));
    }

    /**
     * Deserialize the old blob (allowing only Execution — the old Task class is
     * gone), read the schedule properties off the resulting incomplete class
     * and build a fully-configured ExecuteSchedulableCommandTask.
     */
    private function buildReplacementTask(string $serialized, int $uid): ?ExecuteSchedulableCommandTask
    {
        $oldObj = @unserialize($serialized, [
            'allowed_classes' => [Execution::class],
        ]);
        if (!$oldObj instanceof \__PHP_Incomplete_Class) {
            return null;
        }
        $props = (array)$oldObj;

        // Property keys on protected/private members have null-byte prefixes:
        //   protected → "\0*\0<name>"
        //   private   → "\0<ClassFqn>\0<name>"
        $read = static function (string $name) use ($props) {
            $protectedKey = "\0*\0" . $name;
            if (array_key_exists($protectedKey, $props)) {
                return $props[$protectedKey];
            }
            if (array_key_exists($name, $props)) {
                return $props[$name];
            }
            foreach ($props as $key => $value) {
                if (is_string($key) && substr($key, 0, 1) === "\0" && substr($key, -strlen($name) - 1) === "\0" . $name) {
                    return $value;
                }
            }
            return null;
        };

        $arguments  = $read('arguments');
        $storagePid = is_array($arguments) ? ($arguments['storagePid'] ?? null) : null;
        if ($storagePid === null || $storagePid === '') {
            return null;
        }

        $task = GeneralUtility::makeInstance(ExecuteSchedulableCommandTask::class);
        $task->setCommandIdentifier('abavo_search:update');
        $task->setArguments(['storagePid' => (string)$storagePid]);
        $task->setTaskUid($uid);

        $disabled = $read('disabled');
        if (is_bool($disabled)) {
            $task->setDisabled($disabled);
        }
        $runOnNextCronJob = $read('runOnNextCronJob');
        if (is_bool($runOnNextCronJob)) {
            $task->setRunOnNextCronJob($runOnNextCronJob);
        }
        $executionTime = $read('executionTime');
        if (is_int($executionTime) || (is_string($executionTime) && ctype_digit($executionTime))) {
            $task->setExecutionTime((int)$executionTime);
        }
        $description = $read('description');
        if (is_string($description)) {
            $task->setDescription($description);
        }
        $taskGroup = $read('taskGroup');
        if (is_int($taskGroup) || (is_string($taskGroup) && ctype_digit($taskGroup))) {
            $task->setTaskGroup((int)$taskGroup);
        }
        $execution = $read('execution');
        if ($execution instanceof Execution) {
            $task->setExecution($execution);
        }

        return $task;
    }

    /**
     * Best-effort extraction of a top-level string property from the raw
     * serialized blob without instantiating the old class. Matches both
     * protected/private member framings.
     */
    private function extractStringProperty(string $serialized, string $property): ?string
    {
        // Protected/public shape: s:<len>:"<property>";s:<len>:"<value>";
        // Private shape:         s:<len>:"\0<ClassFqn>\0<property>";s:<len>:"<value>";
        $pattern = '/s:\d+:"(?:\x00[^\x00]*\x00)?' . preg_quote($property, '/') . '";s:\d+:"([^"]*)"/';
        if (preg_match($pattern, $serialized, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function write(string $message): void
    {
        if ($this->output !== null) {
            $this->output->writeln($message);
        }
    }
}
