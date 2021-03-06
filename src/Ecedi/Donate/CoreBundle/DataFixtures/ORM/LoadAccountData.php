<?php
/**
 * @license http://opensource.org/licenses/MIT?year=2015 MIT
 * @copyright 2015 Agence Ecedi
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @package Donate\Test
 *
 */

namespace Ecedi\Donate\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\ORM\Doctrine as FixturesORM;

class LoadAccountData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/../data/accounts/*.yml', $manager, [
            'locale' => 'fr_FR',
        ]);

        $persister = new FixturesORM($manager);
        $persister->persist($objects);
    }
}
