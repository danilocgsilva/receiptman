<?php

declare(strict_types=1);

namespace App\Command\Dev\FilesContentScaffold;

class ReceiptScaffold implements CommandScaffoldInterface
{
    public static function getContent(string $baseName): string
    {
        return <<<EOF
        <?php

        declare(strict_types=1);

        namespace App\ReceiptApp\Receipts;

        use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
        use App\ReceiptApp\File;
        use Symfony\Component\Yaml\Yaml;

        class {$baseName}Receipt extends ReceiptCommons implements ReceiptInterface
        {
            /**
             * @inheritDoc
             */
            public function getFiles(): array
            {
                return \$files;
            }

            private function buildYamlStructure(): void
            {
                \$this->yamlStructure = [
                    'services' => [
                        \$this->name => [
                            'image' => 'REPLACEME1',
                            'container_name' => \$this->name
                        ]
                    ]
                ];
            }
        }
        EOF;
    }
}
