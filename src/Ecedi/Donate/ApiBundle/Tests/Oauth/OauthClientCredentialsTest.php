<?php

namespace Ecedi\Donate\ApiBundle\Tests\Oauth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
class OauthClientCredentialsTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        //create a client, get id and secret
        $httpClient = static::createClient();
        $container = $httpClient->getContainer();
        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setName('phpunit');
        $client->setRedirectUris(array('https://donate.loc'));
        $client->setAllowedGrantTypes(array(
            'token',
            'client_credentials',
            'authorization_code',
            'password', ));
        $clientManager->updateClient($client);

        $this->client = $client;
    }
    /*
    //TODO voir pourquoi ca ne fonctionne pas
    public function tearDown()
    {
        //Delete oauthClient.
        $httpClient = static::createClient();

        $container = $httpClient->getContainer();
        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $clientManager->deleteClient($this->client);
    }
    */

    public function testNoCredentialAccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/v1/customers', array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json')
        );

        $this->assertEquals(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testCredentialAccess()
    {
        $oauthClient = $this->client;
        //we assert with have a oauthClient
        $this->assertEquals(
            'phpunit',
            $oauthClient->getName(), 'check oauth client availability'
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/oauth/v2/token', array(
            'grant_type' => 'client_credentials',
            'client_id' => $oauthClient->getPublicId(),
            'client_secret' => $oauthClient->getSecret(),
            ));

        //extract token
        $content = $client->getResponse()->getContent();
        $data = json_decode($content);

        //try to access using this token
        $crawler = $client->request('GET', '/api/v1/customers', array(
            'access_token' =>  $data->access_token, ),
             array(),
            array('HTTP_ACCEPT' => 'application/json'));
        $this->assertTrue($client->getResponse()->isSuccessful(), 'check access to endpoint with oauth token');
    }
    /*
    public function testAuthorisationCodeAccess()
    {
        $oauthClient = $this->client;

        //request code
        //PROVIDER_HOST/oauth/v2/auth?client_id=CLIENT_ID&response_type=code&redirect_uri=CLIENT_HOST

        //redirect to login

        //submit login

        //redirect to auth confirmation

        //confirme authorisation

        //find code in the redirect

        //request token
        //PROVIDER_HOST/oauth/v2/token?client_id=CLIENT_ID&client_secret=CLIENT_SECRET&grant_type=authorization_code&redirect_uri=REDIRECT_URL&code=CODE
    }
    */

    /*
    public function testImplicitGrantAccess()
    {
        //request authorisation
        //PROVIDER_HOST/oauth/v2/auth?client_id=CLIENT_ID&redirect_uri=REDIRECT_URL&response_type=token
        //redirect to login

        //submit login

        //redirect to auth confirmation

        //confirme authorisation

        //get token from redirect location
        //CLIENT_HOST/#access_token=ACCESS_TOKEN&expires_in=3600&token_type=bearer&refresh_token=REFRESH_TOKEN

    }
    */

    public function testPasswordAccess()
    {
        $oauthClient = $this->client;
        //we assert with have a oauthClient
        $this->assertEquals(
            'phpunit',
            $oauthClient->getName(), 'check oauth client availability'
        );

        $client = static::createClient();
        $crawler = $client->request('GET', '/oauth/v2/token', array(
            'grant_type' => 'password',
            'client_id' => $oauthClient->getPublicId(),
            'client_secret' => $oauthClient->getSecret(),
            'username' => 'root',
            'password' => 'root',
            ), array(),
            array('HTTP_ACCEPT' => 'application/json'));

  //       $this->assertEquals(
  //   		200,
  //   		$client->getResponse()->getStatusCode()
        // );

        //extract token
        $content = $client->getResponse()->getContent();
        //print_r($content);
        $data = json_decode($content);
        //try to access using this token
        $crawler = $client->request('GET', '/api/v1/customers', array(
            'access_token' =>  $data->access_token, ),
             array(),
            array('HTTP_ACCEPT' => 'application/json'));
        $this->assertTrue($client->getResponse()->isSuccessful(), 'check access to endpoint with oauth token');

        //request token
        //PROVIDER_HOST/oauth/v2/token?client_id=CLIENT_ID&client_secret=CLIENT_SECRET&grant_type=password&username=USERNAME&password=PASSWORD

        //check response
        //{"access_token":"ACCESS_TOKEN","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"REFRESH_TOKEN"}
    }
}
