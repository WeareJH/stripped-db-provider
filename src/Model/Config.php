<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * General Config
     */
    const XML_PATH_ENABLED = 'stripped_db_provider/general/enabled';
    const XML_PATH_PROJECT_NAME = 'stripped_db_provider/general/project_name';

    /**
     * Amazon S3 Bucket Settings
     */
    const XML_PATH_BUCKET_NAME = 'stripped_db_provider/storage/bucket_name';
    const XML_PATH_BUCKET_REGION = 'stripped_db_provider/storage/region';
    const XML_PATH_ACCESS_KEY_ID = 'stripped_db_provider/storage/access_key_id';
    const XML_PATH_SECRET_ACCESS_KEY = 'stripped_db_provider/storage/secret_access_key';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->config->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function getProjectName(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_PROJECT_NAME);
    }

    public function getBucketRegion(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_BUCKET_REGION);
    }

    public function getBucketName(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_BUCKET_NAME);
    }

    public function getAccessKeyId(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_ACCESS_KEY_ID);
    }

    public function getSecretAccessKey(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_SECRET_ACCESS_KEY);
    }
}
