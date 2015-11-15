# doctrine-fixtures-test

Web test case to load yours Doctrines Fixtures

## Example

<?php

namespace AppBundle\Tests\Functional\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineFixturesTest\FixtureTestCase;

    class ArticleRepositoryTest extends FixtureTestCase
    {
        /** @var EntityRepository */
        private $articleRepository;

        public function setUp()
        {
            parent::setUp();

            $doctrine = $this->client->getContainer()->get('doctrine');

            $this->articleRepository = $doctrine->getRepository('AppBundle:Article');
        }

        public function testFindAll()
        {
            $articles = $this->articleRepository->findAll();

            $this->assertEquals('My first article', $articles[0]->getTitle());
        }
    }
    
Have a look to the folloing link for more informations [http://blog.ajouve.com/symfony2/phpunit/doctrine2/2015/11/15/welcome-to-jekyll.html](http://blog.ajouve.com/symfony2/phpunit/doctrine2/2015/11/15/welcome-to-jekyll.html)
