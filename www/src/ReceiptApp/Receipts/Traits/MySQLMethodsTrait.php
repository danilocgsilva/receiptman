<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Traits;

trait MySQLMethodsTrait
{
    private string $mysqlPortRedirection;

    private string $mysqlRootPassword;
    
    public function setMysqlPortRedirection(string $port): static
    {
        $this->mysqlPortRedirection = $port;
        return $this;
    }

    public function setMysqlRootPassword(string $password): static
    {
        $this->mysqlRootPassword = $password;
        return $this;
    }
}
