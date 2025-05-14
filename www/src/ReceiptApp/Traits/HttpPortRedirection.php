<?php

declare(strict_types= 1);

namespace App\ReceiptApp\Traits;

trait HttpPortRedirection
{
    private string $httpPortRedirection;

    public function setHttpPortRedirection(string $httpPortRedirection): static
    {
        $this->httpPortRedirection = $httpPortRedirection;
        return $this;
    }

    public function getHttpPortRedirection(): string
    {
        return $this->httpPortRedirection;
    }
}
