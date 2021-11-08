<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Ifsnop\Mysqldump\Mysqldump;
use Magento\Framework\Shell;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbDumper
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DbTables
     */
    private $dbTables;

    public function __construct(
        Config $config,
        DbTables $dbTables,
        Shell $shell
    ) {
        $this->shell = $shell;
        $this->config = $config;
        $this->dbTables = $dbTables;
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function dumpDb(ProjectMeta $projectMeta): void
    {
        $hostName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST);
        $dbName   = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
        $dumper = new Mysqldump(
            "mysql:host={$hostName};dbname={$dbName}",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            ['skip-definer' => true]
        );

        $dumper->setTableLimits($this->getTableLimits());
        $dumper->start($projectMeta->getLocalAbsoluteFileDumpPath());
    }

    private function getTableLimits(): array
    {
        $tableNameList = $this->dbTables->getStructureOnlyTables();
        $tableLimits = [];
        foreach ($tableNameList as $tableName) {
            $tableLimits[$tableName] = 0;
        }

        return $tableLimits;
    }
}
