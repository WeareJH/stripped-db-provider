<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Magento\Framework\Shell;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbDumper
{
    private $rmDefinerCommand = "LANG=C LC_CTYPE=C LC_ALL=C sed -e 's/DEFINER[ ]*=[ ]*[^*]*\*/\*/'";
    private $rmPasswordWarning = "grep -v 'Warning: Using a password'";
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
        $this->shell->execute($this->buildStructureDumpForStrippedTablesCmd($projectMeta));
        $this->shell->execute($this->buildDataDumpExcludingStrippedTablesCmd($projectMeta));
    }

    /**
     * @return string
     */
    private function buildDataDumpExcludingStrippedTablesCmd(ProjectMeta $projectMeta): string
    {
        $dumpCmd = "mysqldump --single-transaction --quick -h%s -u%s --password=%s %s %s | %s | %s >> %s";
        return sprintf(
            $dumpCmd,
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME),
            $this->buildListOfStructureOnlyTables(true),
            $this->rmPasswordWarning,
            $this->rmDefinerCommand,
            $projectMeta->getLocalAbsoluteFileDumpPath()
        );
    }

    /**
     * @return string
     */
    private function buildStructureDumpForStrippedTablesCmd(ProjectMeta $projectMeta): string
    {
        $dumpCmd = "mysqldump --single-transaction --quick --no-data -h%s -u%s --password=%s %s %s | %s | %s > %s";
        return sprintf(
            $dumpCmd,
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME),
            $this->buildListOfStructureOnlyTables(false),
            $this->rmPasswordWarning,
            $this->rmDefinerCommand,
            $projectMeta->getLocalAbsoluteFileDumpPath()
        );
    }

    /**
     * @param bool $ignoreTableFlag
     * @return string
     */
    private function buildListOfStructureOnlyTables(bool $ignoreTableFlag = false): string
    {
        $tableNameList = $this->dbTables->getStructureOnlyTables();
        if ($ignoreTableFlag) {
            $dbName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
            $tableNameList = array_map(function ($tableName) use ($dbName) {
                return sprintf(
                    "--ignore-table=%s.%s",
                    $dbName,
                    $tableName
                );
            }, $tableNameList);
        }

        return implode(" ", $tableNameList);
    }
}
