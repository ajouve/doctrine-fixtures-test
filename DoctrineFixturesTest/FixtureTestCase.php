<?php

namespace DoctrineFixturesTest;

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\NullOutput;

abstract class FixtureTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->application = new Application($this->client->getKernel());
        $this->application->setAutoExit(false);

        $this->executeCommand(new CreateDatabaseDoctrineCommand(), new ArrayInput([]));
        $this->executeCommand(new MigrationsMigrateDoctrineCommand(), new ArrayInput([]));
        $this->executeCommand(new LoadDataFixturesDoctrineCommand(), new ArrayInput([]));
    }

    public function tearDown()
    {
        $this->executeCommand(new DropDatabaseDoctrineCommand(), new ArrayInput(['--force' => true]));
        parent::tearDown();
    }

    private function executeCommand(Command $command, Input $input)
    {
        $command->setApplication($this->application);
        $input->setInteractive(false);

        if ($command instanceof ContainerAwareCommand) {
            $command->setContainer($this->client->getContainer());
        }

        $command->run($input, new NullOutput());
    }
}
