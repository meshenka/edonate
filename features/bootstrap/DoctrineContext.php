<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;

/**
 * Hack to avoid behat complaining about deprecated methods
 */
error_reporting(error_reporting() & ~E_USER_DEPRECATED);

class DoctrineContext implements KernelAwareContext
{
    use KernelDictionary;

    protected static $isDbBuild = false;

    /**
     * @BeforeScenario @database
     */
    public function buildDatabase(BeforeScenarioScope $scope)
    {
        if (true === self::$isDbBuild) {
            return;
        }

        $this->doBuildDatabase();
        self::$isDbBuild = true;
    }

    /**
     * @BeforeScenario @fixtures
     */
    public function loadFixtures(BeforeScenarioScope $scope)
    {
        /**
         * Fixtures need database
         */
        $this->buildDatabase($scope);

        /**
         * Purge data for ALL EntityManagers
         * DoctrineFixtures only purge data for the default EntityManager
         */
        $this->doPurgeFixtures();

        $this->doLoadFixtures();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(AfterScenarioScope $scope)
    {
        $managers = $this->getContainer()->get('doctrine')->getManagerNames();
        foreach ($managers as $name => $service) {
            $this->getEntityManager($name)->clear();
        }
    }

    protected function doBuildDatabase()
    {
        $managers = $this->getContainer()->get('doctrine')->getManagerNames();
        foreach ($managers as $name => $service) {
            $this->buildSchema($name);
        }
    }

    protected function doPurgeFixtures()
    {
        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        $managers = $this->getContainer()->get('doctrine')->getManagerNames();
        foreach ($managers as $name => $service) {
            $purger->setEntityManager($this->getEntityManager($name));
            $purger->purge();
        }
    }

    protected function doLoadFixtures()
    {
        $paths = array();
        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/ORM';
        }

        $loader = new DataFixturesLoader($this->getContainer());
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }

        $executor = new ORMExecutor($this->getEntityManager());
        $executor->execute($fixtures, true);
    }

    /**
     * @return null
     */
    protected function buildSchema($name, $update = false)
    {
        $metadata = $this->getEntityManager($name)->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $tool = new SchemaTool($this->getEntityManager($name));

            if (true === $update) {
                $tool->updateSchema($metadata);
            } else {
                $tool->dropSchema($metadata);
                $tool->createSchema($metadata);
            }
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager($name = null)
    {
        return $this->getContainer()->get('doctrine')->getManager($name);
    }
}
