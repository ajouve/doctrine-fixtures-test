<?php

namespace DoctrineFixturesTest;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;

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

        $this->generateSchema();
        $this->loadFixtures();
    }

    protected function generateSchema()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($entityManager);
        $tool->createSchema($metadata);
    }

    protected function loadFixtures()
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $loader = new DataFixturesLoader($this->client->getContainer());

        $paths = array();
        foreach ($this->client->getKernel()->getBundles() as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/ORM';
        }

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
