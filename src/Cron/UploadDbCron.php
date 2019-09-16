<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Cron;

use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\DbDumper;
use Jh\StrippedDbProvider\Model\DbUploader;
use Psr\Log\LoggerInterface;

class UploadDbCron
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DbDumper
     */
    private $dbDumper;

    /**
     * @var DbUploader
     */
    private $dbUploader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Config $config,
        DbDumper $dbDumper,
        DbUploader $dbUploader,
        LoggerInterface $logger
    ) {

        $this->config = $config;
        $this->dbDumper = $dbDumper;
        $this->dbUploader = $dbUploader;
        $this->logger = $logger;
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
            $this->dbDumper->dumpDb();
            $this->dbUploader->uploadDBDump($this->dbDumper->getAbsoluteDumpPath());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        } finally {
            $this->dbDumper->cleanUp();
        }
    }
}
