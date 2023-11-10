<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Ifsnop\Mysqldump\Mysqldump;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbImporter
{
    public function __construct(
        private Config $config
    ) {
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function importDatabase(ProjectMeta $projectMeta): void
    {
        $hostName = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST);
        $dbName   = $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME);
        $dumper = new Mysqldump(
            "mysql:host={$hostName};dbname={$dbName}",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            ['skip-definer' => true]
        );
        $dumper->restore($projectMeta->getLocalAbsoluteFileDumpPath());
    }
}
