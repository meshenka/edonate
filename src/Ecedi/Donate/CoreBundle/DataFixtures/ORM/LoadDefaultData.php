<?php
/**
 * @license http://opensource.org/licenses/MIT?year=2015 MIT
 * @copyright 2015 Agence Ecedi
 * @package Donate\Test
 */

namespace Ecedi\Donate\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\ORM\Doctrine as FixturesORM;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Génère des données de base pour les tests
 * pour les customers, themes et questions
 */
class LoadDefaultData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/../data/default/*.yml', $manager, [
            'providers' => [$this],
            'locale' => 'fr_FR',
        ]);

        $persister = new FixturesORM($manager);
        $persister->persist($objects);
    }
}
