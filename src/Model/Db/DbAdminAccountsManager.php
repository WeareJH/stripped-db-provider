<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Ifsnop\Mysqldump\Mysqldump;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;
use Magento\Framework\Shell;

class DbAdminAccountsManager
{
    private const BACKUP_FILENAME = 'admin_accounts.sql';

    public function __construct(
        private Config $config,
        private Shell $shell
    ) {
    }

    /**
     * @throws \Exception
     */
    public function backupAdminAccounts(ProjectMeta $projectMeta): void
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
                'skip-triggers' => true,
                'include-tables' => [
                    'admin_passwords',
                    'admin_system_messages',
                    'admin_user',
                    'admin_user_session',
                    'authorization_role',
                    'authorization_rule'
                ]
            ]
        );

        $dumper->start($this->getAdminAccountsBackupFilePath($projectMeta));
    }

    public function restoreAdminAccountsBackup(ProjectMeta $projectMeta): void
    {
        $hostName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST);
        $dbName   = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
        $dumper = new Mysqldump(
            "mysql:host={$hostName};dbname={$dbName}",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            ['skip-definer' => true, 'add-drop-table' => true, 'skip-triggers' => true]
        );
        $dumper->restore($this->getAdminAccountsBackupFilePath($projectMeta));
    }

    public function cleanUp(ProjectMeta $projectMeta): void
    {
        try {
            $this->shell->execute("rm %s", [$this->getAdminAccountsBackupFilePath($projectMeta)]);
        } catch (\Exception $e) {
            //empty
        }
    }

    private function getAdminAccountsBackupFilePath(ProjectMeta $projectMeta): string
    {
        return $projectMeta->getLocalDumpStoragePath() . self::BACKUP_FILENAME;
    }
}
