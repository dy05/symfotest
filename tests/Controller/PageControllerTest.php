<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\DataCollector\MessageDataCollector;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class PageControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page');

        self::assertResponseIsSuccessful();
    }

    public function testH1HelloPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page');

        $this->assertSelectorTextContains('h1', 'Hello Page');
    }

//    public function testAuthPageIsRestricted(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', '/main');
//
//        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//    }

    public function testRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/main');

        $this->assertResponseRedirects('/login');
    }

    public function testAuthUserHaveAccess(): void
    {
        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $users = $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml',
        ]);

        $client->loginUser($users['user_test']);

        $client->request('GET', '/main');

        $this->assertResponseIsSuccessful();
    }

    public function testAuthUserSendMailToAdmin(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $users = $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml',
        ]);

        /** @var User $authUser */
        $authUser = $users['user_test'];
        $client->loginUser($authUser);

        $client->request('GET', '/mail');

        $this->assertResponseIsSuccessful();

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('mailer');
        $events = $mailCollector->getEvents()->getEvents();
        $this->assertCount(1, $events);

        /** @var Email $message */
        $message = $events[0]->getMessage();
        $this->assertEquals(new Address('admin@test.com'), $message->getTo()[0]);
        $this->assertEquals('New connexion of ' . $authUser->getEmail(), $message->getSubject());
    }

    public function testAuthUserHaveNoAccessToAdminRoute(): void
    {
        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $users = $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml',
        ]);

        $client->loginUser($users['user_test']);

        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminUserHaveAccess(): void
    {
        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $users = $databaseTool->loadAliceFixture([
            __DIR__ . '/users.yaml',
        ]);

        $client->loginUser($users['user_admin']);

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }
}
