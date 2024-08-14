<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Console;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Jh\StrippedDbProvider\Model\DbFacade;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class ImportFromRemoteCommand extends Command
{
    private const ARGUMENT_PROJECT_NAME = 'source-project-name';
    private const OPTION_NO_ADMIN_ACCOUNT_BACKUP = 'no-admin-backup';
    private const OPTION_CONFIG_DATA_BACKUP = 'config-backup';

    private CONST VALUE_CONFIG_SKIP = 'skip';
    private CONST VALUE_CONFIG_PRESERVE = 'preserve-local';
    private CONST VALUE_CONFIG_MERGE = 'merge';

    private const SUCCESS = 1;

    public function __construct(
        private DbFacade $dbFacade,
        string $name = null
    ) {
        parent::__construct($name);
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
        $this->addOption(
            self::OPTION_NO_ADMIN_ACCOUNT_BACKUP,
            null,
            InputOption::VALUE_OPTIONAL,
            'Set this flag to skip backup of local admin accounts',
            false
        );
        $this->addOption(
            self::OPTION_CONFIG_DATA_BACKUP,
            null,
            InputOption::VALUE_OPTIONAL,
            'Set this flag to back up the whole core_config_data table',
            self::VALUE_CONFIG_MERGE
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Are you sure you want to import database from S3? It will override current environment database [yes/no]',
            false,
            '/^yes/i'
        );

        if (!$helper->ask($input, $output, $question)) {
            return self::SUCCESS;
        }

        try {
            $sourceProjectMeta = new ProjectMeta($input->getArgument(self::ARGUMENT_PROJECT_NAME));
            $backupAdminAccounts = !$input->getOption(self::OPTION_NO_ADMIN_ACCOUNT_BACKUP);
            $backupConfig = $this->getConfigBackupOption($input);

            $output->writeln('<fg=cyan;options=bold>Downloading Database From Cloud Storage...</>');
            $this->dbFacade->downloadDatabaseDump($sourceProjectMeta);
            $output->writeln(sprintf(
                "<info>Dump downloaded at %s</info>",
                $sourceProjectMeta->getLocalAbsoluteCompressedFileDumpPath()
            ));
            $output->writeln('<fg=cyan;options=bold>Uncompressing Database ...</>');
            $this->dbFacade->uncompressDatabaseDump($sourceProjectMeta);
            $output->writeln('<fg=cyan;options=bold>Dropping local tables ...</>');

            if ($backupAdminAccounts) {
                $output->writeln('<fg=cyan;options=bold>Backing up local admin accounts ...</>');
                $this->dbFacade->backupLocalAdminAccounts($sourceProjectMeta);
            }

            if ($backupConfig === self::VALUE_CONFIG_PRESERVE) {
                $output->writeln('<fg=cyan;options=bold>Backing up config ...</>');
                $this->dbFacade->backupLocalConfig($sourceProjectMeta);
            } else if ($backupConfig === self::VALUE_CONFIG_MERGE) {
                $output->writeln('<fg=cyan;options=bold>Backing up preconfigured config paths ...</>');
                $this->dbFacade->backupConfigValues($sourceProjectMeta);
            }

            $output->writeln("<info>Starting the Database import</info>");
            $this->dbFacade->importDatabaseDump($sourceProjectMeta);
            $output->writeln("<info>Database successfully imported.</info>");

            if ($backupAdminAccounts) {
                $output->writeln('<fg=cyan;options=bold>Restoring local admin accounts ...</>');
                $this->dbFacade->restoreLocalAdminAccountsFromBackup($sourceProjectMeta);
            }

            if ($backupConfig === self::VALUE_CONFIG_PRESERVE) {
                $output->writeln('<fg=cyan;options=bold>Restoring config ...</>');
                $this->dbFacade->restoreLocalConfigFromBackup($sourceProjectMeta);
            } else if ($backupConfig === self::VALUE_CONFIG_MERGE) {
                $output->writeln('<fg=cyan;options=bold>Restoring preconfigured config paths ...</>');
                $this->dbFacade->restoreConfigValues();
            }
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        } finally {
            if (isset($sourceProjectMeta)) {
                $this->dbFacade->cleanUpLocalDumpFiles($sourceProjectMeta);
            }
            if (isset($backupAdminAccounts) && $backupAdminAccounts) {
                $this->dbFacade->cleanUpAdminAccountsBackup($sourceProjectMeta);
            }
            if (isset($backupConfig) && $backupConfig === self::VALUE_CONFIG_PRESERVE) {
                $this->dbFacade->cleanUpConfigBackup($sourceProjectMeta);
            }
        }

        return self::SUCCESS;
    }

    private function getConfigBackupOption(InputInterface $input): string
    {
        $configBackupOption = $input->getOption(self::OPTION_CONFIG_DATA_BACKUP);
        if (!in_array($configBackupOption, [self::VALUE_CONFIG_SKIP, self::VALUE_CONFIG_PRESERVE, self::VALUE_CONFIG_MERGE])) {
            $configBackupOption = self::VALUE_CONFIG_MERGE;
        }
        return $configBackupOption;
    }
}
