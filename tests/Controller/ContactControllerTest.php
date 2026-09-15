<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact_back');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact Us');
    }

    public function testSendContactWithInvalidEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact_back');
        $form = $crawler->selectButton('Submit')
            ->form([
                'contact[name]' => 'test',
                'contact[phone]' => '0606060606',
                'contact[email]' => 'test',
                'contact[message]' => 'This is a new message',
                'contact[rgpd]' => true,
            ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Please enter a valid email address.');
    }

    public function testSendContactWithNoRgpd()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact_back');
        $form = $crawler->selectButton('Submit')
            ->form([
                'contact[name]' => 'test',
                'contact[phone]' => '0606060606',
                'contact[email]' => 'test@test.com',
                'contact[message]' => 'This is a new message',
            ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Please accept rgpd.');
    }

    public function testSendContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact_back');
        $form = $crawler->selectButton('Submit')
            ->form([
                'contact[name]' => 'john',
                'contact[phone]' => '0606060606',
                'contact[email]' => 'test@test.com',
                'contact[message]' => 'This is a new message',
                'contact[rgpd]' => true,
            ]);

        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Your mail successfully sent.');
    }
}
