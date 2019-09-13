<?php

declare(strict_types=1);

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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Upload DB Command</info>');

        try {
            $this->dbDumper->dumpDb();

        } catch (\Exception $e) {

        }

    }
}