<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Jh\StrippedDbProvider\Model\DbFacade;
use Jh\StrippedDbProvider\Model\Config;

class UploadToRemoteCommand extends Command
{
    private const OPTION_FULL_DUMP = 'full';

    public function __construct(private DbFacade $dbFacade, private Config $config, string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('wearejh:stripped-db-provider:upload-to-remote');
        $this->setDescription("Upload a stripped DB dump to JH's Cloud Storage");
        $this->addOption(self::OPTION_FULL_DUMP);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fullDump =  (bool) $input->getOption(self::OPTION_FULL_DUMP);
            $projectMeta = $this->config->getProjectMeta();
            $output->writeln('<fg=cyan;options=bold>Dumping Database...</>');
            $this->dbFacade->dumpDatabase($projectMeta, $fullDump);
            $output->writeln("<info>Dump created at {$projectMeta->getLocalAbsoluteFileDumpPath()}</info>");
            $output->writeln('<fg=cyan;options=bold>Compressing Dump...</>');
            $this->dbFacade->compressDatabaseDump($projectMeta);
            $output->writeln('<fg=cyan;options=bold>Uploading Dump to Cloud Storage...</>');
            $this->dbFacade->uploadDatabaseDump($projectMeta);
            $output->writeln("<info>Dump successfully uploaded at {$projectMeta->getRemoteDumpObjectKey()}.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        } finally {
            if (isset($projectMeta)) {
                $this->dbFacade->cleanUpLocalDumpFiles($projectMeta);
            }
        }
        return 0;
    }
}
