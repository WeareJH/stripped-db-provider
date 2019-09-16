<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class DbUploader
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $dumpAbsolutePath
     * @return \Aws\ResultInterface
     * @throws \RuntimeException
     */
    public function uploadDBDump(string $dumpAbsolutePath)
    {
        $client = $this->getS3Client();

        $uploader = new MultipartUploader($client, $dumpAbsolutePath, [
            'bucket' => $this->config->getBucketName(),
            'key' => 'stripped-db-backups/' . basename($dumpAbsolutePath)
        ]);

        return $uploader->upload();
    }

    private function getS3Client(): S3Client
    {
        if (is_null($this->client)) {
            $credentials = new Credentials(
                $this->config->getAccessKeyId(),
                $this->config->getSecretAccessKey()
            );

            $this->client = new S3Client([
                'version' => '2019-09-16',
                'region' => 'eu-west-1',
                'credentials' => $credentials
            ]);
        }

        return $this->client;
    }
}
