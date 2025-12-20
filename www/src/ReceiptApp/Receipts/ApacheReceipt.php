<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\ApacheQuestions;
use App\ReceiptApp\Receipts\Interfaces\{
    HttpReportableInterface,
    ReceiptInterface
};
use App\ReceiptApp\Traits\HttpPortRedirection;
use Symfony\Component\Filesystem\Filesystem;

class ApacheReceipt extends ReceiptCommons implements ReceiptInterface, HttpReportableInterface
{
    use HttpPortRedirection;

    private bool $exposewww = false;

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new ApacheQuestions())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        // $this->buildYamlStructure();

        // return [
        //     new File(
        //         "docker-compose.yml", 
        //         Yaml::dump($this->yamlStructure, 4, 2),
        //         $this->fs
        //     )
        // ];

        // The only required file is docker-compose.yml, but it will be taylored afterwards.
        return [];
    }

    public function onExposeWWW(): static
    {
        $this->exposewww = true;
        return $this;
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'image' => 'httpd:latest',
                'container_name' => $this->name
            ]
        ];

        if (isset($this->httpPortRedirection)) {
            $this->yamlStructure[$this->name]['ports'][] = sprintf('%s:80', $this->httpPortRedirection);
        }

        if ($this->exposewww) {
            $this->yamlStructure[$this->name]['volumes'][] = './html:/var/www/html';
        }

        if ($this->networkModeHost) {
            $this->yamlStructure[$this->name]['network_mode'] = 'host';
        }
    }
}
