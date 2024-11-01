<?php

namespace App\ReceiptApp;

class PhpDevMysqlQuestions
{
    private array $propertyQuestionPair;
    
    public function __construct()
    {
        $this->propertyQuestionPair = [
            ["setName", "Write the container name\n"],
            ["setHttpPortRedirection", "Write the port number redirection for http\n"],
            ["setMysqlPortRedirection", "Write the port number redirection for mysql\n"],
            ["setMysqlRootPassword", "Write the mysql root password\n"]
        ];
    }

    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
