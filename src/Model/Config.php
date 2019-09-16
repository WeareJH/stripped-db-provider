<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_PROJECT_NAME = 'wearejh/stripped-db-provider/project_name';
    const XML_PATH_BUCKET_NAME = 'wearejh/stripped-db-provider/bucket_name';
    const XML_PATH_ACCESS_KEY_ID = 'wearejh/stripped-db-provider/access_key_id';
    const XML_PATH_SECRET_ACCESS_KEY = 'wearejh/stripped-db-provider/secret_access_key';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getProjectName(): string
    {
        return (string) $this->config->getValue(self::XML_PATH_FILE_NAME);
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
