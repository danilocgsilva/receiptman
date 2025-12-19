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

class Apache extends ReceiptCommons implements ReceiptInterface, HttpReportableInterface
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
        $this->buildYamlStructure();

        return [
            new File(
                "docker-compose.yml", 
                Yaml::dump($this->yamlStructure, 4, 2),
                $this->fs
            )
        ];
    }

    public function onExposeWWW(): static
    {
        $this->exposewww = true;
        return $this;
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'httpd:latest',
                    'container_name' => $this->name
                ]
            ]
        ];

        if (isset($this->httpPortRedirection)) {
            $this->yamlStructure['services'][$this->name]['ports'][] = sprintf('%s:80', $this->httpPortRedirection);
        }

        if ($this->exposewww) {
            $this->yamlStructure['services'][$this->name]['volumes'][] = './html:/var/www/html';
        }

        if ($this->networkModeHost) {
            $this->yamlStructure['services'][$this->name]['network_mode'] = 'host';
        }
    }
}
