<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Ifsnop\Mysqldump\Mysqldump;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;
use Magento\Framework\Shell;

class DbConfigBackupManager
{
    private const BACKUP_FILENAME = 'core_config_data.sql';

    public function __construct(
        private Config $config,
        private Shell $shell,
        private ResourceConnection $resourceConnection,
        private array $configCache = []
    ) {
    }

    /**
     * @throws \Exception
     */
    public function backupConfig(ProjectMeta $projectMeta): void
    {
        $hostName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST);
        $dbName   = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
        $dumper = new Mysqldump(
            "mysql:host={$hostName};dbname={$dbName}",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            [
                'skip-definer' => true,
                'add-drop-table' => true,
                'include-tables' => [
                    'core_config_data'
                ]
            ]
        );

        $dumper->start($this->getConfigBackupFilePath($projectMeta));
    }

    public function restoreConfigBackup(ProjectMeta $projectMeta): void
    {
        $hostName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST);
        $dbName   = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
        $dumper = new Mysqldump(
            "mysql:host={$hostName};dbname={$dbName}",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            ['skip-definer' => true, 'add-drop-table' => true, 'skip-triggers' => true]
        );
        $dumper->restore($this->getConfigBackupFilePath($projectMeta));
    }

    public function cleanUp(ProjectMeta $projectMeta): void
    {
        try {
            $this->shell->execute("rm %s", [$this->getConfigBackupFilePath($projectMeta)]);
        } catch (\Exception $e) {
            //empty
        }
    }

    public function cacheConfigValuesToKeep(): void
    {
        $pathsToCache = $this->config->getConfigPathsToKeep();
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()
            ->from($tableName,['*']);

        foreach ($pathsToCache as $pattern) {
            $select->orWhere('path LIKE ?', $pattern);
        }

        $this->configCache = $connection->fetchAll($select);
    }

    public function restoreConfigValuesToKeep(): void
    {
        foreach ($this->configCache as $config) {
            $this->resourceConnection->getConnection()->insertOnDuplicate(
                $this->resourceConnection->getTableName('core_config_data'),
                $config
            );
        }
    }

    private function getConfigBackupFilePath(ProjectMeta $projectMeta): string
    {
        return $projectMeta->getLocalDumpStoragePath() . self::BACKUP_FILENAME;
    }
}
