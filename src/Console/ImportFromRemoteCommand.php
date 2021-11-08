<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Jh\StrippedDbProvider\Model\DbFacade;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class ImportFromRemoteCommand extends Command
{
    const ARGUMENT_PROJECT_NAME = 'source-project-name';

    /**
     * @var DbFacade
     */
    private $dbFacade;

    public function __construct(
        DbFacade $dbFacade,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dbFacade = $dbFacade;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('wearejh:stripped-db-provider:import-from-remote');
        $this->setDescription("Import DB from JH's Cloud Storage");
        $this->addArgument(
            self::ARGUMENT_PROJECT_NAME,
            InputArgument::REQUIRED,
            'Source Project Name to import from eg. prod-stroustrup-workshop.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $sourceProjectMeta = new ProjectMeta($input->getArgument(self::ARGUMENT_PROJECT_NAME));
            $output->writeln('<fg=cyan;options=bold>Downloading Database From Cloud Storage...</>');
            $this->dbFacade->downloadDatabaseDump($sourceProjectMeta);
            $output->writeln(sprintf(
                "<info>Dump downloaded at %s</info>",
                $sourceProjectMeta->getLocalAbsoluteCompressedFileDumpPath()
            ));
            $output->writeln('<fg=cyan;options=bold>Uncompressing Database ...</>');
            $this->dbFacade->uncompressDatabaseDump($sourceProjectMeta);
            $output->writeln('<fg=cyan;options=bold>Importing Database ...</>');
            $this->dbFacade->importDatabaseDump($sourceProjectMeta);
            $output->writeln("<info>Database successfully imported.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        } finally {
            if (isset($sourceProjectMeta)) {
                $this->dbFacade->cleanUpLocalDumpFiles($sourceProjectMeta);
            }
        }
    }
}
