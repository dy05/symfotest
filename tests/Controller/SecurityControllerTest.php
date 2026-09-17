<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends WebTestCase
{
    public function testDisplayLogin()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => 'test@test.com',
                'password' => 'password0'
            ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin()
    {
        $client = static::createClient();

        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml',
        ]);

        /*
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')
            ->form([
                'email' => 'test@test.com',
                'password' => 'password'
            ]);

        $client->submit($form);
        */
        $client->request('GET', '/');

        $container = static::getContainer();
        $csrfToken = $container->get('security.csrf.token_generator')->generateToken();
        $session = $client->getSession();
        $session->set('_csrf/authenticate', $csrfToken);
        $session->save();
        $session->start();

        $client->request(
            'POST',
            '/login',
            [
                'email' => 'test@test.com',
                'password' => 'password',
                '_csrf_token' => $csrfToken
            ]
         );

        $this->assertResponseRedirects('/main');
//        $client->followRedirect();
//        $this->assertSelectorExists('.alert.alert-success');
    }
}
