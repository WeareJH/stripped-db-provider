<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Jh\StrippedDbProvider\Model\S3\ClientProvider;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;
use GuzzleHttp\Psr7\Stream;

class DbDownloader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientProvider
     */
    private $clientProvider;

    public function __construct(ClientProvider $clientProvider, Config $config)
    {
        $this->config = $config;
        $this->clientProvider = $clientProvider;
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function downloadDBDump(ProjectMeta $projectMeta)
    {
        $client = $this->clientProvider->getClient();
        $bucketName = $this->config->getBucketName();
        $objectKey  = $projectMeta->getRemoteDumpObjectKey();

        if (!$client->doesObjectExist($bucketName, $objectKey)) {
            throw new \RuntimeException(
                sprintf(
                    "Object '%s' does not exist in bucket '%s'",
                    $objectKey,
                    $bucketName
                )
            );
        }

        $result = $client->getObject([
            'Bucket' => $bucketName,
            'Key' => $objectKey
        ]);

        if (!$result->get('Body') instanceof Stream) {
            throw new \RuntimeException("Wrong response received from the remote");
        }

        /**
         * @var $stream Stream
         */
        $stream = $result->get('Body');
        $localStorageFilePath = $projectMeta->getLocalAbsoluteCompressedFileDumpPath();
        $success = file_put_contents(
            $localStorageFilePath,
            $stream->getContents()
        );

        if (!$success) {
            throw new \RuntimeException("Could not write DB to " . $localStorageFilePath);
        }
    }
}
