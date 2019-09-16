<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\Process\PhpExecutableFinderFactory;
use Symfony\Component\Process\PhpExecutableFinder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;

class DbDumper
{
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PhpExecutableFinder
     */
    private $phpFinder;

    public function __construct(
        Config $config,
        PhpExecutableFinderFactory $phpFinderFactory,
        Shell $shell
    ) {
        $this->shell = $shell;
        $this->config = $config;
        $this->phpFinder = $phpFinderFactory->create();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function dumpDb(): string
    {
        $phpPath = $this->phpFinder->find() ?: "php";
        $cmd = "$phpPath -f ./vendor/bin/n98-magerun2 db:dump";
        $cmdArgsTemplate = "--root-dir=%s --strip=%s --compression=%s --no-interaction %s";
        $cmdArgs = [BP, "@stripped @development", "gzip", $this->getAbsoluteDumpPath()];
        return $this->shell->execute($cmd . ' ' . $cmdArgsTemplate, $cmdArgs);
    }

    /**
     * Attempt to silently remove the database dumps
     * @return string
     */
    public function cleanUp()
    {
        try {
            $this->shell->execute("rm %s", [$this->getAbsoluteDumpPath()]);
        } catch (\Exception $e) {
            //empty
        }
    }

    /**
     * @return string
     */
    public function getAbsoluteDumpPath(): string
    {
        $fileName = sprintf("%s.sql.gz", $this->config->getProjectName());
        return BP . '/var/' . $fileName;
    }
}
