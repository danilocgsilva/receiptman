<?php

declare(strict_types=1);

namespace App\Command\Dev\FilesContentScaffold;

class QuestionsScaffold implements CommandScaffoldInterface
{
    public static function getContent(string $baseName): string
    {
        return <<<EOF
        <?php

        declare(strict_types=1);

        namespace App\ReceiptApp\Receipts\Questions;

        use App\ReceiptApp\Receipts\Questions\Types\InputType;
        use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

        class {$baseName}Questions extends BaseQuestion implements QuestionInterface
        {
            public function __construct()
            {
                parent::__construct();
            }
        }
        EOF;
    }
}
