<?php

declare(strict_types=1);

namespace App\ReceiptApp\Traits;
trait PutGenericDatabase
{
    public function putGenericDatabase()
    {
        $databaseReceipt = [
            'image' => 'mysql:latest',
            'container_name' => $this->name . '_db',
            'environment' => [
                sprintf('MYSQL_ROOT_PASSWORD=%s', $this->mysqlRootPassword)
            ],
            'ports' => [
                sprintf('%s:3306', $this->mysqlPortRedirection)
            ]
        ];

        $this->yamlStructure['services'][$this->name . "_db"] = $databaseReceipt;
    }
}
