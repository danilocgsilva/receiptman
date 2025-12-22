<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\Questions\MySQLQuestions;
use App\ReceiptApp\Receipts\Traits\MySQLMethodsTrait;

class MySQLReceipt extends ReceiptCommons implements ReceiptInterface
{
    use MySQLMethodsTrait;

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new MySQLQuestions())->getPropertyQuestionPair();
    }

    protected function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'image' => 'mysql:latest',
                'container_name' => $this->name,
                'environment' => [
                    sprintf('MYSQL_ROOT_PASSWORD=%s', $this->mysqlRootPassword)
                ],
                'ports' => [
                    sprintf('%s:3306', $this->mysqlPortRedirection)
                ]
            ]
        ];
    }
}