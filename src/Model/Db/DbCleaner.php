<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Jh\StrippedDbProvider\Model\ProjectMeta;
use Magento\Framework\Shell;

class DbCleaner
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
     * Attempt to silently remove the database dumps
     *
     * @param ProjectMeta $projectMeta
     * @return string
     */
    public function cleanUp(ProjectMeta $projectMeta)
    {
        try {
            $this->shell->execute("rm %s", [$projectMeta->getLocalAbsoluteFileDumpPath()]);
            $this->shell->execute("rm %s", [$projectMeta->getLocalAbsoluteCompressedFileDumpPath()]);
        } catch (\Exception $e) {
            //empty
        }
    }
}
