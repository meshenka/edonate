<?php

namespace Ecedi\Donate\OgoneBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OgoneControllerTest extends WebTestCase
{
    public function testPayWithoutSession()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ogone/pay');

        $response = $client->getResponse();

        $response = $client->getResponse();
        $this->assertTrue($response->isForbidden());
    }

    /**
     * verification du bon affichage du front office en français
     */
    public function testPayWithValidSession()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $session = $container->get('session');
        $session->set('intentId', '434'); //TODO trouver une solution pour tester comme il faut, sans doute créer au setup un intent
        $session->save();

        $crawler = $client->request('GET', '/ogone/pay');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());

        $this->assertTrue($crawler->filter('h1:contains("Je donne")')->count() == 1);

        $this->assertTrue($crawler->filter('body:contains("Dans quelques secondes, vous serez redirigé vers la page de paiement.")')->count() == 1);

        $this->assertTrue($crawler->filter('form#goto-ogone')->count() == 1);
        $this->assertTrue($crawler->filter('input[type=submit]')->count() == 1);
    }

    // TODO
    public function testPostsaleWithoutParams()
    {
        $client = static::createClient();

        $client->enableProfiler();
        $crawler = $client->request('GET', '/ogone/api/postsale');
        $response = $client->getResponse();
        $profiler = $client->getProfile($response);

        //TODO verifier ce qu'il se passe dans cette requête.
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
