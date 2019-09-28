<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Jh\StrippedDbProvider\Model\Db;

class DbFacade
{
    /**
     * @var Db\DbDumper
     */
    private $dumper;

    /**
     * @var Db\DbCompresser
     */
    private $compresser;

    /**
     * @var Db\DbDownloader
     */
    private $downloader;

    /**
     * @var Db\DbUploader
     */
    private $uploader;

    /**
     * @var Db\DbCleaner
     */
    private $cleaner;

    /**
     * @var Db\DbImporter
     */
    private $importer;

    public function __construct(
        Db\DbDumper $dumper,
        Db\DbCompresser $compresser,
        Db\DbDownloader $downloader,
        Db\DbUploader $uploader,
        Db\DbImporter $importer,
        Db\DbCleaner $cleaner
    ) {
        $this->dumper = $dumper;
        $this->compresser = $compresser;
        $this->downloader = $downloader;
        $this->uploader = $uploader;
        $this->cleaner = $cleaner;
        $this->importer = $importer;
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function dumpDatabase(ProjectMeta $projectMeta)
    {
        $this->dumper->dumpDb($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function downloadDatabaseDump(ProjectMeta $projectMeta)
    {
        $this->downloader->downloadDBDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function compressDatabaseDump(ProjectMeta $projectMeta)
    {
        $this->compresser->compressDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function uncompressDatabaseDump(ProjectMeta $projectMeta)
    {
        $this->compresser->uncompressDump($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws \Exception
     */
    public function importDatabaseDump(ProjectMeta $projectMeta)
    {
        $this->importer->importDatabase($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function cleanUpLocalDumpFiles(ProjectMeta $projectMeta)
    {
        $this->cleaner->cleanUp($projectMeta);
    }

    /**
     * @param ProjectMeta $projectMeta
     */
    public function uploadDatabaseDump(ProjectMeta $projectMeta)
    {
        $this->uploader->uploadDBDump($projectMeta);
    }
}
