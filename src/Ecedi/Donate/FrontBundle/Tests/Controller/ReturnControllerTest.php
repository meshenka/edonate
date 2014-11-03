<?php

namespace Ecedi\Donate\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReturnControllerTest extends WebTestCase
{

    /**
     * verification du bon affichage du front office en français
     */
    public function testCompletedWithSession()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $session = $container->get('session');
        $session->set('intentId', '434'); //TODO trouver une solution pour tester comme il faut, sans doute créer au setup un intent
        $session->save();

        $crawler = $client->request('GET', '/fr/completed');

        $this->assertTrue($crawler->filter('h1:contains("Votre don est validé")')->count() == 1);
        $this->assertTrue($crawler->filter('h2:contains("Détail du paiement")')->count() == 1);

        //TODO tester le contenu dynamique de la page

    }

    public function testCompletedWithoutSession()
    {
        $client = static::createClient();

        $client->request('GET', '/fr/completed');

        $response = $client->getResponse();
        $this->assertTrue($response->isClientError());

    }

    public function testCanceled()
    {
        $client = static::createClient();

        $client->request('GET', '/fr/canceled');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());

    }

    public function testDenied()
    {
        $client = static::createClient();

        $client->request('GET', '/fr/denied');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());

    }

    public function testFailed()
    {
        $client = static::createClient();

        $client->request('GET', '/fr/failed');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());

    }

}
