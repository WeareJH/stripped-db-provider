<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Aws\ResultInterface;
use Aws\S3\MultipartUploader;
use Jh\StrippedDbProvider\Model\S3\ClientProvider;
use Jh\StrippedDbProvider\Model\Config;
use Jh\StrippedDbProvider\Model\ProjectMeta;

class DbUploader
{
    public function __construct(private ClientProvider $clientProvider, private Config $config)
    {
    }

    /**
     * @param ProjectMeta $projectMeta
     * @return ResultInterface
     */
    public function uploadDBDump(ProjectMeta $projectMeta): ResultInterface
    {
        $client = $this->clientProvider->getClient();

        $uploader = new MultipartUploader($client, $projectMeta->getLocalAbsoluteCompressedFileDumpPath(), [
            'bucket' => $this->config->getBucketName(),
            'key' => $projectMeta->getRemoteDumpObjectKey()
        ]);

        return $uploader->upload();
    }
}
