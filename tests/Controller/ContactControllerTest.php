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

    public function testAjaxRender(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact_ajax');

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
            ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.form-error-message', 'This value is not valid email address.');
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
            ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.form.error-message', 'This value is not valid email address.');
    }
}
