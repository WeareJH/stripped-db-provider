<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Magento\Framework\Shell;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbImporter
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config,
        Shell $shell
    ) {
        $this->shell = $shell;
        $this->config = $config;
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function importDatabase(ProjectMeta $projectMeta): void
    {
        $cmd = sprintf(
            "mysql -h%s -u%s --password=%s %s < %s",
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_HOST),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_USER),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_PASSWORD),
            $this->config->getLocalDbConfigData(ConfigOptionsListConstants::KEY_NAME),
            $projectMeta->getLocalAbsoluteFileDumpPath()
        );

        $this->shell->execute($cmd);
    }
}
