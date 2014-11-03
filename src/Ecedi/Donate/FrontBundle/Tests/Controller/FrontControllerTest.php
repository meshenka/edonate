<?php

namespace Ecedi\Donate\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\BrowserKit\Cookie;
//use Symfony\Component\BrowserKit\CookieJar;

class FrontControllerTest extends WebTestCase
{
    /**
     * verification du bon affichage du front office en français
     */
    public function testIndexInFrench()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('h1:contains("Je donne")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("Je donne")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("Mes coordonnées personnelles")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("Mon reçu fiscal")')->count() == 1);

        $this->assertTrue($crawler->filter('form')->count() == 1);
        $this->assertTrue($crawler->filter('input[type=submit]')->count() == 1);

    }

    /**
     * verification du bon affichage du front office en anglais
     */
    public function testIndexInEnglish()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en');

        $this->assertTrue($crawler->filter('h1:contains("I give")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("I Give")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("My personal details")')->count() == 1);
        $this->assertTrue($crawler->filter('h3:contains("My tax receipt")')->count() == 1);

        $this->assertTrue($crawler->filter('form')->count() == 1);
        $this->assertTrue($crawler->filter('input[type=submit]')->count() == 1);

    }

   /**
     * Soumission du formulaire, on va vers la page Ogone
     *
     */
    public function testSubmitValidData()
    {
        //$client = static::createClient(['environment' => 'test', 'debug' => true]);
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Donner')->form();

        $crawler = $client->submit($form,
            array(
                'donate[amount_preselected]' => 'manual',
                'donate[amount_manual]' => '50',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertTrue(
            $client->getResponse()->isRedirect('/ogone/pay')
        );

    }

    /**
     * Soumission du formulaire avec à la fois un montant manuel et un montant auto sélectionné
     */
    public function testSubmitInconsistentAmount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Donner')->form();

        $crawler = $client->submit($form,
            array(
                'donate[amount_preselected]' => '10',
                'donate[amount_manual]' => '50',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertTrue(
            $client->getResponse()->isRedirect('/ogone/pay')
        );

        $crawler = $client->followRedirect();

        //on verifier le montant dans le formulaire ogone
        $input = $crawler->filter('input[name="AMOUNT"]');
        $this->assertEquals('1000', $input->attr('value'),'Amount selection failed failed');

    }

    /**
     * Soumission du formulaire avec à la fois un montant manuel et un montant auto sélectionné
     */
    public function testSubmitPrelectedAmount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Donner')->form();

        $crawler = $client->submit($form,
            array(
                'donate[amount_preselected]' => '10',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertTrue(
            $client->getResponse()->isRedirect('/ogone/pay')
        );

        $crawler = $client->followRedirect();

        //on verifier le montant dans le formulaire ogone
        $input = $crawler->filter('input[name="AMOUNT"]');
        $this->assertEquals('1000', $input->attr('value'),'Preselected Amount selection failed');

    }

    /**
     * Soumission du formulaire avec à la fois un montant manuel et un montant auto sélectionné
     */
    public function testSubmitManualAmount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Donner')->form();

        $crawler = $client->submit($form,
            array(
                'donate[amount_preselected]' => 'manual',
                'donate[amount_manual]' => '660',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertTrue(
            $client->getResponse()->isRedirect('/ogone/pay')
        );

        $crawler = $client->followRedirect();

        //on verifier le montant dans le formulaire ogone
        $input = $crawler->filter('input[name="AMOUNT"]');
        $this->assertEquals('66000', $input->attr('value'),'Preselected Amount selection failed');

    }

    /**
     * verification que le formualire ne passe qu'avec les champs requis
     */
    public function testSubmitWithoutRequiredInputs()
    {
        $client = static::createClient();


        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Donner')->form();


        $client->enableProfiler();
        $crawler = $client->submit($form,
            array(
                'donate[amount_preselected]' => '10',
                'donate[amount_manual]' => '50',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'test@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $profiler = $client->getProfile($response);
        if ($profiler) {
            $this->assertEquals('donate_front_home', $profiler->getCollector('request')->getRoute(), 'We are back on home');
            return;
        } 

        $this->assertTrue('false');        

    }

    /**
     * Soumission du formulaire avec le tracker de campagne
     */
    public function testSubmitCampaignTrackerByCookie()
    {
        $client = static::createClient();
        $cookieJar = $client->getCookieJar();
        $cookieJar->set(new Cookie('__utmz', '1.1386025859.5.5.utmcsr=apis.google.com|utmccn=phpunit|utmcmd=referral|utmcct=/u/0/wm/4/_/widget/render/comments',time() + 3600 * 24 * 7, '/', 'localhost', false, false));

        $container = $client->getContainer();
        //$campaign =  $container->getParameter('donate_front.campaign');
        $crawler = $client->request('GET', '/');//, [$campaign => 'phpunit']);

        $form = $crawler->selectButton('Donner')->form();


        $crawler = $client->submit($form,
            array(
                'donate[amount_manual]' => '660',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $crawler = $client->followRedirect();

        $orderId = $crawler->filter('input[name="ORDERID"]')->attr('value');

        $prefix =  $container->getParameter('donate_ogone.prefix');

        if (strpos($orderId, $prefix. '-') === 0) {

            $intentId = (int) str_replace($prefix. '-', '', $orderId);
            $intentRepository = $container->get('doctrine')->getManager()->getRepository('DonateCoreBundle:Intent');

            $intent = $intentRepository->find($intentId);

            $this->assertEquals('phpunit', $intent->getCampaign(), 'Intent capture request utm campaign');
            return;
        
        }
        
        $this->assertTrue(false, 'cannot find intentId from OrderID');
        
    }

    public function testSubmitCampaignTrackerByQuery()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $campaign =  $container->getParameter('donate_front.campaign');
        $crawler = $client->request('GET', '/', [$campaign => 'phpunit']);

        $form = $crawler->selectButton('Donner')->form();

        $crawler = $client->submit($form,
            array(
                'donate[amount_manual]' => '660',
                'donate[firstName]' => 'Integration',
                'donate[lastName]' => 'Test Suite',
                'donate[email][first]' => 'support@ecedi.fr',
                'donate[email][second]' => 'support@ecedi.fr',
                'donate[addressStreet]' => '91 avenue de la République',
                'donate[addressZipcode]' => '75011',
                'donate[addressCity]' => 'Paris',
                'donate[addressCountry]' => 'FR',
                'donate[erf]' => '0',
                'donate[payment_method]' => 'ogone',
            )
        );

        $crawler = $client->followRedirect();

        $orderId = $crawler->filter('input[name="ORDERID"]')->attr('value');

        $prefix =  $container->getParameter('donate_ogone.prefix');

        if (strpos($orderId, $prefix. '-') === 0) {

            $intentId = (int) str_replace($prefix. '-', '', $orderId);
            $intentRepository = $container->get('doctrine')->getManager()->getRepository('DonateCoreBundle:Intent');

            $intent = $intentRepository->find($intentId);

            $this->assertEquals('phpunit', $intent->getCampaign(), 'Intent capture request utm campaign');
            return;
        } 
        
        $this->assertTrue(false, 'cannot find intentId from OrderID');        

    }

}
