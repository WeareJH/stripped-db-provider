<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Encryption\EncryptorInterface;

class Config
{
    /**
     * General Config
     */
    private const XML_PATH_ENABLED = 'stripped_db_provider/general/enabled';
    private const XML_PATH_PROJECT_NAME = 'stripped_db_provider/general/project_name';

    /**
     * Amazon S3 Bucket Settings
     */
    private const XML_PATH_BUCKET_NAME = 'stripped_db_provider/storage/bucket_name';
    private const XML_PATH_BUCKET_REGION = 'stripped_db_provider/storage/region';
    private const XML_PATH_ACCESS_KEY_ID = 'stripped_db_provider/storage/access_key_id';
    private const XML_PATH_SECRET_ACCESS_KEY = 'stripped_db_provider/storage/secret_access_key';

    /**
     * Dump Specific
     */
    const XML_PATH_PROJECT_IGNORE_TABLES = 'stripped_db_provider/dump/project_ignore_tables';

    public function __construct(
        private readonly ScopeConfigInterface $config,
        private readonly DeploymentConfig $deploymentConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) $this->config->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function getProjectMeta(): ProjectMeta
    {
        return new ProjectMeta(
            (string) $this->config->getValue(self::XML_PATH_PROJECT_NAME)
        );
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
        $accessId = $this->config->getValue(self::XML_PATH_ACCESS_KEY_ID);
        return $this->encryptor->decrypt($accessId);
    }

    public function getSecretAccessKey(): string
    {
        $accessKey = $this->config->getValue(self::XML_PATH_SECRET_ACCESS_KEY);
        return $this->encryptor->decrypt($accessKey);
    }

    public function getLocalDbConfigData(string $key): ?string
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
