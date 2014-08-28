<?php
// Génère des données de base pour les tests
namespace Ecedi\Donate\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;

class LoadDefaultData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     *
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        ini_set('memory_limit', '2048M');
        for ($i = 1; $i <= 6000; $i++) {
            $customer = new Customer();
            $customer->setCreatedAt(self::getRandomDate());
            $customer->setFirstName(self::getRandomFirstname());
            $customer->setLastName(self::getRandomLastname());
            $customer->setCivility(self::getRandomCivility());
            $customer->setEmail(self::getRandomEmail($customer->getLastName()));
            $customer->setAddressStreet(self::getRandomAddressStreet());
            $customer->setBirthday(self::getRandomDate());
            $customer->setPhone(self::getRandomPhone());
            $customer->setCompany(self::getRandomCompany());
            $customer->setRemoteId(self::getRandomRemoteId());
            $customer->setWebsite(self::getRandomWebsite());
            $customer->setAddressZipcode(self::getRandomZipCode());
            $customer->setAddressCity(self::getRandomCity());
            $customer->setAddressCountry(self::getRandomCountry());

            $CustomerOgoneID = $customer->getLastName() . '_OGONE_' . self::getRandomRemoteId();

            $manager->persist($customer);

            $jmax = rand(1, 3);
            $j = 1;

            if (($i % 1000) == 0) {
                $manager->flush();
            }

            for ($j = 1; $j <= $jmax; $j++) {
                $amount = self::getRandomAmount();
                $paymentMethod = self::getRandomPaymentMethod();
                $campaign = self::getRandomCampaign();

                $intent = new Intent($amount, $paymentMethod, 'EUR', $campaign);
                $intent->setCustomer($customer);
                $intent->setCreatedAt(self::getRandomDate());
                $intent->setType(self::getRandomType());
                $intent->setStatus(self::getRandomStatus());
                $intent->setFiscalReceipt(self::getRandomFiscalReceipt());

                $manager->persist($intent);

                $pmax = 1;
                if ($intent->getType() == Intent::TYPE_RECURING) {
                    $pmax = rand(1, 15);
                }
                for ($p = 1; $p <= $pmax; $p++) {
                    $payment = new Payment();
                    $payment->setIntent($intent);
                    if ($pmax == 1 && $p == $pmax) {
                        $payment->setStatus(self::getRandomPaymentStatus());
                    } else {
                        $payment->setStatus(Payment::STATUS_PAYED);
                    }
                    $payment->setAlias($CustomerOgoneID);
                    $payment->setTransaction(self::getRandomTransactionId());
                    $payment->setAutorisation(self::getRandomAutorisationId());

                    $manager->persist($payment);
                }
            }
        }
        $manager->flush();
    }

    public static function getRandomFirstname()
    {
        return self::getRandomString(array(
            'Aurélia', 'Audrey', 'Vanina', 'Lucas', 'Cécile', 'Marie-Ange',
            'Abdelaziz', 'Frédéric', 'Yohann', 'Thierry', 'Alexandre',
            'Nicolas', 'Sylvain', 'Lieng', 'David', 'Jennifer', 'Amélie',
            'Gaël', 'Mathieu', 'Camila',
        ));
    }

    public static function getRandomLastname()
    {
        return self::getRandomString(array(
            'Petit', 'Durand', 'Dubois', 'Moreau', 'Lefebvre', 'Leroy',
            'Roux', 'Morel', 'Fournier', 'Girard', 'Bonnet', 'Dupont',
            'Lambert', 'Fontaine', 'Rousseau', 'Muller', 'Lefevre',
        ));
    }

    public static function getRandomCivility()
    {
        /*$civilities = $this->container->getParameters('donate_front.form.civility');

        return self::getRandomString($civilities);*/
        return self::getRandomString(array(
            'Mr', 'Mme', 'Ms', 'Miss', 'Dr', 'Prof', 'Company',
        ));
    }

    public static function getRandomAddressStreet()
    {
        return self::getRandomString(array(
            '2 rue ...', '3 Avenue ...', '4 bd ...', '2 rue Casimir Brenier', '3 rue Lefebvre', '4 rue Leroy',
            '7 rue Roux', '12 Avenue Morel', '4 impasse Fournier', ' 2 rue de Vos Girards', '8 rue Bonnet', '4 rue Dupont',
            '5 rue Lambert', ' 7 rue Fontaine', '4 rue Rousseau', '3 bd Muller', '7 rue Lefevre',
        ));
    }

    public static function getRandomRemoteId()
    {
        return rand(10000000, 99999999);
    }

    public static function getRandomEmail($lastname)
    {
        $randEmail = $lastname . '_' . rand(1, 1000) . '@' . self::getRandomMailBox();

        return $randEmail;
    }

    public static function getRandomDate()
    {
        $randDate = new \DateTime();
        $rand = rand(0, 10000);
        $randDate->sub(new \DateInterval("P{$rand}D"));

        return $randDate;
    }

    public static function getRandomPhone()
    {
        $ind = self::getRandomString(array('01', '02', '03', '04','05', '07', '08', '09'));

        return $ind . rand(10000000, 99999999);
    }

    public static function getRandomCompany()
    {
        return self::getRandomString(array(
            'Ecedi', 'W3C', 'JQuery', 'PHPnet', 'Symfony',
        ));
    }

    public static function getRandomWebsite()
    {
        $randSite = 'Http://' . self::getRandomLastname().'@ecedi.com';

        return $randSite;
    }

    public static function getRandomZipCode()
    {
        return self::getRandomString(array(
            '38000', '75000', '42000', '42100', '69000', '13000',
        ));
    }

    public static function getRandomCity()
    {
        return self::getRandomString(array(
            'Grenoble', 'Paris', 'Lyon', 'Saint-Etienne', 'Marseille', 'Nice',
        ));
    }

    public static function getRandomCountry()
    {
        return self::getRandomString(array(
            'FR', 'EN', 'IT', 'GH', 'GS', 'GI', 'US', 'DE'
        ));
    }

    public static function getRandomAmount()
    {
        return self::getRandomString(array(
            '2000', '5000', '8000', '10000', '20000', '40000', '60000', '100000' // en cents
        ));
    }

    public static function getRandomPaymentMethod()
    {
        return self::getRandomString(array(
            'Ponctuel', 'Périodique', 'Prélèvement automatique', 'Promesse de don par chèque'
        ));
    }

    public static function getRandomType()
    {
        return self::getRandomString(array(
            Intent::TYPE_SPOT, Intent::TYPE_RECURING
        ));
    }

    public static function getRandomStatus()
    {
        return self::getRandomString(array(
            Intent::STATUS_NEW, Intent::STATUS_PENDING, Intent::STATUS_DONE, Intent::STATUS_CANCEL, Intent::STATUS_ERROR,
            Intent::STATUS_DONE,Intent::STATUS_DONE,Intent::STATUS_DONE,Intent::STATUS_DONE
        ));
    }

    public static function getRandomCampaign()
    {
        return self::getRandomString(array(
            'Campagne évènemetielle', 'Campagne Annuelle', 'Campagne trimestrielle', 'Campagne bimensuelle'
        ));
    }

    public static function getRandomFiscalReceipt()
    {
        return self::getRandomString(array(
            Intent::FISCAL_RECEIP_EMAIL, Intent::FISCAL_RECEIP_POST,
        ));
    }

    public static function getRandomMailBox()
    {
        return self::getRandomString(array(
            'yahoo.fr', 'hotmail.fr', 'google.com','ecedi.fr'
        ));
    }

    public static function getRandomTransactionId()
    {
        return 'TRANSACT_' . rand(1000000, 9999999);
    }

    public static function getRandomAutorisationId()
    {
        return 'AUTORIZED_' . rand(1000000, 9999999);
    }

    private static function getRandomPaymentStatus()
    {
        return self::getRandomString(Payment::getAllowedStatus());
    }

    private static function getRandomString($array)
    {
        $r = rand(0, count($array) - 1);

        return $array[$r];
    }
}
