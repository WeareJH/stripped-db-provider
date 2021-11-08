<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Cron;

use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\DbFacade;
use Psr\Log\LoggerInterface;

class UploadDbCron
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DbFacade
     */
    private $dbFacade;

    public function __construct(
        Config $config,
        DbFacade $dbFacade,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->dbFacade = $dbFacade;
    }

    /**
     * Dump and Upload Stripped DB to the Cloud
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        try {
            $projectMeta = $this->config->getProjectMeta();
            $this->dbFacade->dumpDatabase($projectMeta);
            $this->dbFacade->compressDatabaseDump($projectMeta);
            $this->dbFacade->uploadDatabaseDump($projectMeta);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        } finally {
            if (isset($projectMeta)) {
                $this->dbFacade->cleanUpLocalDumpFiles($projectMeta);
            }
        }
    }
}
