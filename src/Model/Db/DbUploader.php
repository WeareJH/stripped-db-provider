<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Aws\S3\MultipartUploader;
use Jh\StrippedDbProvider\Model\S3\ClientProvider;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbUploader
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
     * @return \Aws\ResultInterface
     */
    public function uploadDBDump(ProjectMeta $projectMeta)
    {
        $client = $this->clientProvider->getClient();

        $uploader = new MultipartUploader($client, $projectMeta->getLocalAbsoluteCompressedFileDumpPath(), [
            'bucket' => $this->config->getBucketName(),
            'key' => $projectMeta->getRemoteDumpObjectKey()
        ]);

        return $uploader->upload();
    }
}
