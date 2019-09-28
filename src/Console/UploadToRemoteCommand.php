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
    /**
     * @var DbFacade
     */
    private $dbFacade;

    /**
     * @var Config
     */
    private $config;

    public function __construct(DbFacade $dbFacade, Config $config, string $name = null)
    {
        parent::__construct($name);
        $this->dbFacade = $dbFacade;
        $this->config = $config;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('wearejh:stripped-db-provider:upload-to-remote');
        $this->setDescription("Upload a stripped DB dump to JH's Cloud Storage");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $projectMeta = $this->config->getProjectMeta();
            $output->writeln('<fg=cyan;options=bold>Dumping Database...</>');
            $this->dbFacade->dumpDatabase($projectMeta);
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
    }
}
