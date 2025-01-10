<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PhpDevMysql;
use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\{
    GetSpecificFileTrait,
    HasQuestionWithMethod
};
use App\ReceiptApp\File;
use Error;

class PhpDevMysqlTest extends TestCase
{
    use GetSpecificFileTrait;
    use HasQuestionWithMethod;

    private PhpDevMysql $phpDevMysql;

    function setUp(): void
    {
        $this->phpDevMysql = new PhpDevMysql();
    }

    public function testDockerComposeContent(): void
    {
        $this->phpDevMysql->setName("testing_env")
            ->setHttpPortRedirection("6000")
            ->setMysqlPortRedirection("4006")
            ->setMysqlRootPassword("testing_password");

        $expectedContent = <<<EOF
            services:
              testing_env:
                build:
                  context: .
                container_name: testing_env
                volumes:
                  - './www:/var/www'
                ports:
                  - '6000:80'
                working_dir: /var/www
              testing_env_db:
                image: 'mysql:latest'
                container_name: testing_env_db
                environment:
                  - MYSQL_ROOT_PASSWORD=testing_password
                ports:
                  - '4006:3306'

            EOF;

        $fileDockerfile = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $this->assertSame($expectedContent, $fileDockerfile->content);
    }

    public function testAppFolder(): void
    {
        $this->phpDevMysql->setName("testing_env")
            ->setHttpPortRedirection("6000")
            ->setMysqlPortRedirection("4006")
            ->setMysqlRootPassword("testing_password")
            ->setAppFolder();

        $expectedContent = <<<EOF
            services:
              testing_env:
                build:
                  context: .
                container_name: testing_env
                volumes:
                  - './app:/app'
                ports:
                  - '6000:80'
                working_dir: /app
              testing_env_db:
                image: 'mysql:latest'
                container_name: testing_env_db
                environment:
                  - MYSQL_ROOT_PASSWORD=testing_password
                ports:
                  - '4006:3306'

            EOF;

        $fileDockerfile = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $this->assertSame($expectedContent, $fileDockerfile->content);
    }

    public function testSsh(): void
    {
        $this->phpDevMysql->setName("testing_env2")
            ->setHttpPortRedirection("4000")
            ->setMysqlPortRedirection(mysqlPortRedirection: "3333")
            ->setMysqlRootPassword("opass2")
            ->setSshVolume();

        $fileDockerCompose = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $expectedContent = <<<EOF
            services:
              testing_env2:
                build:
                  context: .
                container_name: testing_env2
                volumes:
                  - './www:/var/www'
                  - './.ssh/:/root/.ssh'
                ports:
                  - '4000:80'
                working_dir: /var/www
              testing_env2_db:
                image: 'mysql:latest'
                container_name: testing_env2_db
                environment:
                  - MYSQL_ROOT_PASSWORD=opass2
                ports:
                  - '3333:3306'

            EOF;

        $this->assertSame($expectedContent, $fileDockerCompose->content);
    }

    public function testCountFiles(): void
    {
        $this->phpDevMysql->setName("count_me")
            ->setHttpPortRedirection("5000")
            ->setMysqlPortRedirection(mysqlPortRedirection: "4333")
            ->setMysqlRootPassword("opass2");

        $this->assertCount(5, $this->phpDevMysql->getFiles());
    }

    public function testGetSpecificFiles(): void
    {
        $this->phpDevMysql->setName("specific_files")
            ->setHttpPortRedirection("5000")
            ->setMysqlPortRedirection(mysqlPortRedirection: "4333")
            ->setMysqlRootPassword("opass3");

        $receiptFiles = $this->phpDevMysql->getFiles();

        $this->assertInstanceOf(
            File::class,
            $this->getSpecificFile($receiptFiles, "docker-compose.yml")
        );
        $this->assertInstanceOf(
            File::class,
            $this->getSpecificFile($receiptFiles, "Dockerfile")
        );
        $this->assertInstanceOf(
            File::class,
            $this->getSpecificFile($receiptFiles, "www/html/index.php")
        );
        $this->assertInstanceOf(
            File::class,
            $this->getSpecificFile($receiptFiles, "config/startup.sh")
        );
        $this->assertInstanceOf(
            File::class,
            $this->getSpecificFile($receiptFiles, "config/xdebug.ini")
        );
    }

    public function testSetPublicFolderAsHost(): void
    {
        $this->phpDevMysql->setName(name: "public_root")
            ->setHttpPortRedirection(httpPortRedirection: "5000")
            ->setMysqlPortRedirection(mysqlPortRedirection: "4333")
            ->setMysqlRootPassword(mysqRootPassword: "opass3")
            ->setPublicFolderAsHost();

        $receiptFiles = $this->phpDevMysql->getFiles();

        $this->assertCount(6, $receiptFiles);
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->phpDevMysql->getPropertyQuestionsPairs();
        $this->assertInstanceOf(
            expected: QuestionEntry::class,
            actual: $questionsParis[0]
        );
    }

    public function testQuestionsAvailable(): void
    {
        $questionsPairs = $this->phpDevMysql->getPropertyQuestionsPairs();
        $this->assertCount(8, $questionsPairs);
    }

    public function testCheckEachQuestion(): void
    {
        $questionsPairs = $this->phpDevMysql->getPropertyQuestionsPairs();

        $this->assertTrue($this->hasQuestionWithMethod("setName", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setNetworkModeHost", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setSshVolume", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setHttpPortRedirection", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setMysqlPortRedirection", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setMysqlRootPassword", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setPublicFolderAsHost", $questionsPairs));
    }

    public function testQuestionsNoMysql(): void
    {
        $this->phpDevMysql->setNoDatabase();
        $questionsPairs = $this->phpDevMysql->getPropertyQuestionsPairs();

        $this->assertTrue($this->hasQuestionWithMethod("setName", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setNetworkModeHost", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setSshVolume", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setHttpPortRedirection", $questionsPairs));
        $this->assertTrue($this->hasQuestionWithMethod("setPublicFolderAsHost", $questionsPairs));
        $this->assertFalse($this->hasQuestionWithMethod("setMysqlPortRedirection", $questionsPairs));
        $this->assertFalse($this->hasQuestionWithMethod("setMysqlRootPassword", $questionsPairs));
        $this->assertCount(6, $questionsPairs);
    }

    public function testGetFiles(): void
    {
        $this->phpDevMysql
            ->setName(name: "the_beloved_environment.")
            ->setHttpPortRedirection("2013")
            ->setMysqlPortRedirection(mysqlPortRedirection: "3433")
            ->setMysqlRootPassword("mysupersecurepassword");

        $files = $this->phpDevMysql->getFiles();

        $this->assertCount(5, $files);
    }

    public function testGetFilesWithoutRequiringDatabase(): void
    {
        $this->phpDevMysql
            ->setName(name: "the_beloved_environment.")
            ->setHttpPortRedirection("2013")
            ->setNoDatabase();

        $files = $this->phpDevMysql->getFiles();

        $this->assertCount(5, $files);
    }

    public function testForgetSetContainerNameAndGetFiles(): void
    {
        $this->expectException(Error::class);
        
        $this->phpDevMysql
            ->setName(name: "the_beloved_environment.");

        $this->phpDevMysql->getFiles();
    }

    public function testGetDockerFile()
    {
        $expectedContent = <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install curl git zip -y
        RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
        RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
        COPY config/xdebug.ini /etc/php/8.2/mods-available/
        COPY config/startup.sh /startup.sh
        RUN chmod +x /startup.sh

        CMD /startup.sh
        EOF;

        $this->phpDevMysql
            ->setName(name: "docker_file_content_test.")
            ->setHttpPortRedirection("2024")
            ->setNoDatabase();

        $receiptFiles = $this->phpDevMysql->getFiles();

        $dockerFile = $this->getSpecificFile($receiptFiles, "Dockerfile");

        $this->assertSame($expectedContent, $dockerFile->content);
    }

    public function testGetDockerFileForPublicFolder()
    {
        $expectedContent = <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install curl git zip -y
        RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
        RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
        COPY config/xdebug.ini /etc/php/8.2/mods-available/
        COPY config/startup.sh /startup.sh
        COPY config/000-default.conf /etc/apache2/sites-available/
        RUN chmod +x /startup.sh

        CMD /startup.sh
        EOF;

        $this->phpDevMysql
            ->setName(name: "docker_file_content_test.")
            ->setHttpPortRedirection("2024")
            ->setPublicFolderAsHost()
            ->setNoDatabase();

        $receiptFiles = $this->phpDevMysql->getFiles();

        $dockerFile = $this->getSpecificFile($receiptFiles, "Dockerfile");

        $this->assertSame($expectedContent, $dockerFile->content);
    }

    public function testWithNode(): void
    {
        $expectedContent = <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install curl git zip -y
        RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
        RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
        RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - 
        RUN apt-get install -y nodejs
        COPY config/xdebug.ini /etc/php/8.2/mods-available/
        COPY config/startup.sh /startup.sh
        RUN chmod +x /startup.sh

        CMD /startup.sh
        EOF;

        $this->phpDevMysql
            ->setName(name: "docker_file_content_test.")
            ->setHttpPortRedirection("2024")
            ->setNoDatabase()
            ->addNode();

        $receiptFiles = $this->phpDevMysql->getFiles();

        $dockerFile = $this->getSpecificFile($receiptFiles, "Dockerfile");
    
        $this->assertSame($expectedContent, $dockerFile->content);
    }
}
