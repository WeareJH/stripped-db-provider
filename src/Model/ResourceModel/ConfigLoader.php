<?php
declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class ConfigLoader
{
    public function __construct(
        private ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function fetch(array $patterns): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()
            ->from($tableName,['scope', 'scope_id', 'path', 'value']);

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '%')) {
                $select->orWhere('path LIKE ?', $pattern . '%');
            } else {
                $select->orWhere('path = ?', $pattern);
            }
        }

        return $connection->fetchAll($select);
    }

}