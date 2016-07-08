<?php
/**
 * @license http://opensource.org/licenses/MIT?year=2015 MIT
 * @copyright 2015 Agence Ecedi
 */
namespace Ecedi\Donate\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Persister\Doctrine as FixturesORM;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * Génère des données de base pour les tests
 * pour les customers, themes et questions.
 */
class LoadDefaultData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
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

    public function intentStatus()
    {
        return $this->getOne(Intent::getPossibleStatus());
    }

    public function intentType()
    {
        return $this->getOne(Intent::getTypes());
    }

    public function paymentStatus()
    {
        return $this->getOne(Payment::getAllowedStatus());
    }

    public function paymentMethod()
    {
        return $this->getOne(['ogone', 'check_promise', 'sepa_offline']);
    }

    private function getOne($values)
    {
        $one = rand(0, count($values) - 1);

        return $values[$one];
    }
}
