<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

class ReceiptCommons
{
    protected bool $sshVolume = false;
    
    public function setSshVolume(): self
    {
        $this->sshVolume = true;
        return $this;
    }
}
