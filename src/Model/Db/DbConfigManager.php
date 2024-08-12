<?php
declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Db;

use Jh\StrippedDbProvider\Model\ResourceModel\ConfigLoader;
use Magento\Deploy\Model\ConfigWriter;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;

class DbConfigManager
{
    public function __construct(
        private readonly ConfigLoader $configLoader,
        private readonly WriterInterface $configWriter
    )
    {
    }

    public function export() {
        //TODO: get patterns from config
        $patterns = ['payment/adyen_applepay/active'];
        $data = $this->configLoader->fetch($patterns);
        //TODO: use magento way to get var file/folder
        $filePath = 'var/config-exported.csv';
        $file = fopen($filePath, 'w');

        // Adding the headers
        fputcsv($file, ['scope', 'scope_id', 'path', 'value']);

        // Adding the data
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filePath;
    }

    public function restore() {
        $filePath = 'var/config-exported.csv';

        $file = fopen($filePath, 'r');
        fgetcsv($file); // Skip the header

        while (($row = fgetcsv($file)) !== false) {
            list($scope, $scopeId, $path, $value) = $row;
            $this->configWriter->save($path, $value, $scope, $scopeId);
        }

        fclose($file);
    }
}