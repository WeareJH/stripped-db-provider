<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;

class DbDumper
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
     * @return string
     * @throws LocalizedException
     */
    public function dumpDb(): string
    {
        $cmdTemplate = "php -f ./vendor/bin/n98-magerun2 db:dump --root-dir=%s --strip=%s --no-interaction %s";
        $cmdArgs = [BP, "@stripped @development", $this->getAbsoluteDumpPath(false)];
        return $this->shell->execute($cmdTemplate, $cmdArgs);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function compressDump(): string
    {
        return $this->shell->execute("gzip %s", [$this->getAbsoluteDumpPath(false)]);
    }

    /**
     * Attempt to silently remove the database dumps
     * @return string
     */
    public function cleanUp(): string
    {
        try {
            $this->shell->execute("rm %s", [$this->getAbsoluteDumpPath(false)]);
            $this->shell->execute("rm %s", [$this->getAbsoluteDumpPath(true)]);
        } catch (\Exception $e) {
            //empty
        }
    }

    /**
     * @param bool $compressed
     * @return string
     */
    public function getAbsoluteDumpPath(bool $compressed = true): string
    {
        return BP . '/var/db-stripped.sql' . ($compressed ? '.gz' : '');
    }
}
