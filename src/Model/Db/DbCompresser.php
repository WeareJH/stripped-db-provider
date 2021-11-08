<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Jh\StrippedDbProvider\Model\ProjectMeta;
use Magento\Framework\Shell;
use Magento\Framework\Exception\LocalizedException;

class DbCompresser
{
    /**
     * @var Shell
     */
    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws LocalizedException
     */
    public function compressDump(ProjectMeta $projectMeta)
    {
        $this->shell->execute(
            sprintf(
                "gzip -c %s > %s",
                $projectMeta->getLocalAbsoluteFileDumpPath(),
                $projectMeta->getLocalAbsoluteCompressedFileDumpPath()
            )
        );
    }

    /**
     * @param ProjectMeta $projectMeta
     * @throws LocalizedException
     */
    public function uncompressDump(ProjectMeta $projectMeta)
    {
        $this->shell->execute(
            sprintf(
                "gunzip -c %s > %s",
                $projectMeta->getLocalAbsoluteCompressedFileDumpPath(),
                $projectMeta->getLocalAbsoluteFileDumpPath()
            )
        );
    }
}
