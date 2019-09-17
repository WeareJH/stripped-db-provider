<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Jh\StrippedDbProvider\Model\DbDumper;
use Jh\StrippedDbProvider\Model\DbUploader;

class UploadDbCommand extends Command
{
    /**
     * @var DbDumper
     */
    private $dbDumper;

    /**
     * @var DbUploader
     */
    private $dbUploader;

    public function __construct(DbDumper $dbDumper, DbUploader $dbUploader, string $name = null)
    {
        parent::__construct($name);
        $this->dbDumper = $dbDumper;
        $this->dbUploader = $dbUploader;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('wearejh:db:backup-stripped-db');
        $this->setDescription("Upload a stripped DB dump to JH's Cloud Storage");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('<fg=cyan;options=bold>Dumping Database...</>');
            $this->dbDumper->dumpDb();
            $output->writeln("<info>Dump created at {$this->dbDumper->getAbsoluteDumpPath()}</info>");
            $output->writeln('<fg=cyan;options=bold>Uploading Dump to Cloud Storage...</>');
            $this->dbUploader->uploadDBDump($this->dbDumper->getAbsoluteDumpPath());
            $output->writeln("<info>Dump successfully updated.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        } finally {
            $output->writeln('<fg=cyan;options=bold>Cleaning up files...</>');
            $this->dbDumper->cleanUp();
            $output->writeln("<info>Done.</info>");
        }
    }
}
