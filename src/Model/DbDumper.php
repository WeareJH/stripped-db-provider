<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\Process\PhpExecutableFinderFactory;
use Symfony\Component\Process\PhpExecutableFinder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

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
     * @var PhpExecutableFinder
     */
    private $phpFinder;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var DbTables
     */
    private $dbTables;

    public function __construct(
        Config $config,
        DbTables $dbTables,
        PhpExecutableFinderFactory $phpFinderFactory,
        DeploymentConfig $deploymentConfig,
        Shell $shell
    ) {
        $this->shell = $shell;
        $this->config = $config;
        $this->phpFinder = $phpFinderFactory->create();
        $this->deploymentConfig = $deploymentConfig;
        $this->dbTables = $dbTables;
    }

    /**
     * @throws LocalizedException
     */
    public function dumpDb(): void
    {
        $this->shell->execute($this->buildStructureDumpForStrippedTablesCmd());
        $this->shell->execute($this->buildDataDumpExcludingStrippedTablesCmd());
        $this->shell->execute($this->buildCompressDumpCmd());
    }

    /**
     * @return string
     */
    private function buildDataDumpExcludingStrippedTablesCmd(): string
    {
        $dumpCmd = "mysqldump --single-transaction --quick -h%s -u%s --password=%s %s %s | %s | %s >> %s";
        return sprintf(
            $dumpCmd,
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_HOST),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_NAME),
            $this->buildListOfStructureOnlyTables(true),
            $this->rmPasswordWarning,
            $this->rmDefinerCommand,
            $this->getAbsoluteDumpPath(false)
        );
    }

    /**
     * @return string
     */
    private function buildStructureDumpForStrippedTablesCmd(): string
    {
        $dumpCmd = "mysqldump --single-transaction --quick --no-data -h%s -u%s --password=%s %s %s | %s | %s > %s";
        return sprintf(
            $dumpCmd,
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_HOST),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            $this->getDbConfigData(ConfigOptionsListConstants::KEY_NAME),
            $this->buildListOfStructureOnlyTables(false),
            $this->rmPasswordWarning,
            $this->rmDefinerCommand,
            $this->getAbsoluteDumpPath(false)
        );
    }

    /**
     * @return string
     */
    private function buildCompressDumpCmd(): string
    {
        return sprintf(
            "gzip -c %s > %s",
            $this->getAbsoluteDumpPath(false),
            $this->getAbsoluteDumpPath(true)
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
            $dbName = $this->getDbConfigData(ConfigOptionsListConstants::KEY_NAME);
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

    /**
     * Attempt to silently remove the database dumps
     * @return string
     */
    public function cleanUp()
    {
        try {
            $this->shell->execute("rm %s", [$this->getAbsoluteDumpPath()]);
            $this->shell->execute("rm %s", [$this->getAbsoluteDumpPath(false)]);
        } catch (\Exception $e) {
            //empty
        }
    }

    /**
     * @param bool $compressed
     * @return string
     */
    public function getAbsoluteDumpPath(bool $compressed = true): string
    {
        $fileName = sprintf(
            "%s.sql%s",
            $this->config->getProjectName(),
            ($compressed ? '.gz' : '')
        );
        return BP . '/var/' . $fileName;
    }

    /**
     * @param string $key
     * @return string|null
     */
    private function getDbConfigData(string $key): ?string
    {
        return $this->deploymentConfig->get(
            sprintf(
                "%s/%s",
                ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT,
                $key
            )
        );
    }
}
