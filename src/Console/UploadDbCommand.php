<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Jh\StrippedDbProvider\Model\DbDumper;

class UploadDbCommand extends Command
{
    /**
     * @var DbDumper
     */
    private $dbDumper;

    public function __construct(DbDumper $dbDumper, string $name = null)
    {
        parent::__construct($name);
        $this->dbDumper = $dbDumper;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('setup:db:backup-stripped-db');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {
            $output->writeln('<fg=cyan;options=bold>Dumping Database...</>');
            $cmdOutput = $this->dbDumper->dumpDb();
            $output->writeln("<info>{$cmdOutput}</info>");
            $output->writeln('<fg=cyan;options=bold>Compressing Database Dump...</>');
            $this->dbDumper->compressDump();
            $output->writeln("<info>Compressed database is at {$this->dbDumper->getAbsoluteDumpPath()}</info>");

        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        } finally {
            $this->dbDumper->cleanUp();
        }
    }
}
