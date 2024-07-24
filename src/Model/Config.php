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
    private const XML_PATH_USE_ENCRYPTED_S3_CONFIG = 'stripped_db_provider/storage/using_encrypted_values_for_s3_config';

    /**
     * Dump Specific
     */
    const XML_PATH_PROJECT_IGNORE_TABLES = 'stripped_db_provider/dump/project_ignore_tables';

    public function __construct(
        private ScopeConfigInterface $config,
        private DeploymentConfig $deploymentConfig,
        private readonly EncryptorInterface $encrypted
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool) $this->config->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function isEncryptedS3Config(): bool
    {
        return (bool) $this->config->isSetFlag(self::XML_PATH_USE_ENCRYPTED_S3_CONFIG);
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
        return $this->isEncryptedS3Config()
            ? $this->decryptConfigValue((string) $this->config->getValue(self::XML_PATH_ACCESS_KEY_ID))
            : (string) $this->config->getValue(self::XML_PATH_ACCESS_KEY_ID);
    }

    public function getSecretAccessKey(): string
    {
        return $this->isEncryptedS3Config()
            ? $this->decryptConfigValue((string) $this->config->getValue(self::XML_PATH_SECRET_ACCESS_KEY))
            : (string) $this->config->getValue(self::XML_PATH_SECRET_ACCESS_KEY);
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

    private function decryptConfigValue(string $encryptedConfigValue): ?string
    {
        return $this->encrypted->decrypt($encryptedConfigValue);
    }
}
