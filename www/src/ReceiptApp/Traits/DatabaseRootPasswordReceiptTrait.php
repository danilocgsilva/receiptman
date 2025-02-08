<?php

declare(strict_types= 1);

namespace App\ReceiptApp\Traits;

trait DatabaseRootPasswordReceiptTrait
{
    private string $databaseRootPassword;
    
    public function setDatabaseRootPassword(string $databaseRootPassword): static
    {
        $this->databaseRootPassword = $databaseRootPassword;
        return $this;
    }
}
