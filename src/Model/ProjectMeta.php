<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

class ProjectMeta
{
    /**
     * @var string
     */
    private $localStoragePath = BP . '/var/';

    /**
     * @var string
     */
    private $remoteStoragePath = 'stripped-db-backups/';

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->validateName($name);
        $this->name = $name;
    }

    /**
     * @param string $name
     * @throws \RuntimeException
     */
    private function validateName(string $name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if (strlen($ext) > 0) {
            throw new \RuntimeException("Project name '$name' must not contain any dots");
        }
    }

    public function getDumpFileName(): string
    {
        return $this->name . '.sql';
    }

    public function getCompressedDumpFileName(): string
    {
        return $this->getDumpFileName() . '.gz';
    }

    public function getLocalAbsoluteFileDumpPath(): string
    {
        return $this->localStoragePath . $this->getDumpFileName();
    }

    public function getLocalAbsoluteCompressedFileDumpPath(): string
    {
        return $this->localStoragePath . $this->getCompressedDumpFileName();
    }

    public function getRemoteDumpObjectKey(): string
    {
        return $this->remoteStoragePath . $this->getCompressedDumpFileName();
    }
}
