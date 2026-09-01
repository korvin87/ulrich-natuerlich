<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Abavo\AbavoSearch\Command;

use Abavo\AbavoSearch\Service\IndexUpdateService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI: `abavo_search:update <storagePid>`
 *
 * Replaces the removed Extbase CommandController::updateCommand(). Schedulable
 * via the standard TYPO3 "Execute console commands (Scheduler)" task.
 */
class UpdateIndexCommand extends Command
{
    private IndexUpdateService $indexUpdateService;

    public function __construct(IndexUpdateService $indexUpdateService, ?string $name = null)
    {
        parent::__construct($name);
        $this->indexUpdateService = $indexUpdateService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Rebuild the abavo_search index for the given storage page id(s).')
            ->addArgument(
                'storagePid',
                InputArgument::REQUIRED,
                'Storage page id (comma-separated for multiple pids).'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storagePid = (string)$input->getArgument('storagePid');

        try {
            $results = $this->indexUpdateService->updateIndex($storagePid);
        } catch (\Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        if (empty($results)) {
            $output->writeln('<comment>No indexers ran (empty result).</comment>');
            return Command::SUCCESS;
        }

        foreach ($results as $uid => $r) {
            $output->writeln(sprintf(
                'Indexer #%d "%s": %d items in %s',
                (int)$uid,
                (string)$r['title'],
                (int)$r['indexCount'],
                (string)$r['duration']
            ));
        }
        return Command::SUCCESS;
    }
}
