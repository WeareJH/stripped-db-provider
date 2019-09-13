<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;

class DbDumper
{
    const CMD_TEMPLATE = "php -f ./vendor/bin/n98 db:dump --root-dir=%s --no-interaction --strip\"%s\" %s";
    const CMD_ARGUMENTS = [BP, "@stripped @development", BP. '/db-stripped.sql'];

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
        return $this->shell->execute(self::CMD_TEMPLATE, self::CMD_ARGUMENTS);
    }
}
