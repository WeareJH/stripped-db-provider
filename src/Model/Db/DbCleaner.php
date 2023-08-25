<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Jh\StrippedDbProvider\Model\ProjectMeta;
use Magento\Framework\Shell;

class DbCleaner
{
    public function __construct(private Shell $shell)
    {
    }

    public function cleanUp(ProjectMeta $projectMeta): void
    {
        try {
            $this->shell->execute("rm %s", [$projectMeta->getLocalAbsoluteFileDumpPath()]);
            $this->shell->execute("rm %s", [$projectMeta->getLocalAbsoluteCompressedFileDumpPath()]);
        } catch (\Exception $e) {
            //empty
        }
    }
}
