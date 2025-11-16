<?php
// src/Console/Application.php

namespace App;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Application extends BaseApplication
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct('Receiptman', '1.0.0');
        
        $this->container = $container;
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $commands = $this->container->get('console.command_loader')->getNames();
        
        foreach ($commands as $commandName) {
            $this->add($this->container->get('console.command_loader')->get($commandName));
        }
    }
}