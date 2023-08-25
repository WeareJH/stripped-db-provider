<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Jh\StrippedDbProvider\Model\Db;

class DbFacade
{
    public function __construct(
        private Db\DbDumper $dumper,
        private Db\DbCompresser $compresser,
        private Db\DbDownloader $downloader,
        private Db\DbUploader $uploader,
        private Db\DbImporter $importer,
        private Db\DbCleaner $cleaner
    ) {
    }

    /**
     * @param ProjectMeta $projectMeta
     * @param bool $fullDump
     * @throws \Exception
     */
    public function dumpDatabase(ProjectMeta $projectMeta, bool $fullDump): void
    {
        $this->dumper->dumpDb($projectMeta, $fullDump);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function downloadDatabaseDump(ProjectMeta $projectMeta): void
    {
        $this->downloader->downloadDBDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function compressDatabaseDump(ProjectMeta $projectMeta): void
    {
        $this->compresser->compressDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function uncompressDatabaseDump(ProjectMeta $projectMeta): void
    {
        $this->compresser->uncompressDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function importDatabaseDump(ProjectMeta $projectMeta): void
    {
        $this->importer->importDatabase($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function cleanUpLocalDumpFiles(ProjectMeta $projectMeta): void
    {
        $this->cleaner->cleanUp($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function uploadDatabaseDump(ProjectMeta $projectMeta): void
    {
        $this->uploader->uploadDBDump($projectMeta);
    }
}
