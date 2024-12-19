<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\NginxQuestions;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Traits\HttpPortRedirection;
use App\ReceiptApp\Receipts\Interfaces\{
    ReceiptInterface,
    HttpReportableInterface
};

class NginxReceipt extends ReceiptCommons implements ReceiptInterface, HttpReportableInterface
{
    use HttpPortRedirection;
    
    private bool $exposeServerDefaultFile = false;

    public function __construct()
    {
        $this->questionsPairs = (new NginxQuestions())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        if (!isset($this->name)) {
            throw new NotReadyException();
        }
        
        $this->buildYamlStructure();

        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2))
        ];

        if ($this->exposeServerDefaultFile) {
            $files[] = new File("Dockerfile", $this->getDockerfileContent());
            $files[] = new File("config/default.conf", $this->getDefaultServerConf());
        }

        return $files;
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => []
            ]
        ];

        if ($this->exposeServerDefaultFile) {
            $this->yamlStructure['services'][$this->name]['build']['context'] = '.';
        } else {
            $this->yamlStructure['services'][$this->name]['image'] = 'nginx:latest';
        }

        $this->yamlStructure['services'][$this->name]['container_name'] = $this->name;

        if (isset($this->httpPortRedirection)) {
            $this->yamlStructure['services'][$this->name]['ports'][] = sprintf('%s:80', $this->httpPortRedirection);
        }
    }

    public function onExposeDefaultServerFile(): self
    {
        $this->exposeServerDefaultFile = true;
        return $this;
    }

    private function getDefaultServerConf(): string
    {
        return <<<EOF
        server {
            listen       80;
            listen  [::]:80;
            server_name  localhost;

            #access_log  /var/log/nginx/host.access.log  main;

            location / {
                root   /usr/share/nginx/html;
                index  index.html index.htm;
            }

            #error_page  404              /404.html;

            # redirect server error pages to the static page /50x.html
            #
            error_page   500 502 503 504  /50x.html;
            location = /50x.html {
                root   /usr/share/nginx/html;
            }

            # proxy the PHP scripts to Apache listening on 127.0.0.1:80
            #
            #location ~ \.php$ {
            #    proxy_pass   http://127.0.0.1;
            #}

            # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
            #
            #location ~ \.php$ {
            #    root           html;
            #    fastcgi_pass   127.0.0.1:9000;
            #    fastcgi_index  index.php;
            #    fastcgi_param  SCRIPT_FILENAME  /scripts\$fastcgi_script_name;
            #    include        fastcgi_params;
            #}

            # deny access to .htaccess files, if Apache's document root
            # concurs with nginx's one
            #
            #location ~ /\.ht {
            #    deny  all;
            #}
        }

        EOF;
    }

    private function getDockerfileContent(): string
    {
        return <<<EOF
        FROM nginx:latest

        COPY ./config/default.conf /etc/nginx/conf.d/default.conf

        EOF;
    }
}

