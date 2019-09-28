<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\S3;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Jh\StrippedDbProvider\Model\Config;

class ClientProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var S3Client
     */
    private $client;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return S3Client
     */
    public function getClient(): S3Client
    {
        if (is_null($this->client)) {
            $credentials = new Credentials(
                $this->config->getAccessKeyId(),
                $this->config->getSecretAccessKey()
            );

            $this->client = new S3Client([
                'version' => 'latest',
                'region' => $this->config->getBucketRegion(),
                'credentials' => $credentials
            ]);
        }

        return $this->client;
    }


}
