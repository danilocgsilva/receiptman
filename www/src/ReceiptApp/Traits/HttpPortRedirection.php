<?php

namespace App\ReceiptApp\Traits;

trait HttpPortRedirection
{
    private string $httpPortRedirection;

    public function setHttpPortRedirection(string $httpPortRedirection): static
    {
        $this->httpPortRedirection = $httpPortRedirection;
        return $this;
    }
}
