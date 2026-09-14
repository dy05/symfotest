<?php

namespace App\Tests\Controller;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;

class ContactControllerWithJsTest extends PantherTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact Us');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testSendContactWithInvalidEmailPanther()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('Submit')
            ->form([
                'name' => 'test',
                'phone' => '0606060606',
                'email' => 'test',
                'message' => 'This is a new message',
            ]);

        $client->getWebDriver()
            ->findElement(WebDriverBy::name('rgpd'))->click();
        $client->submit($form);
        $client->waitFor('.invalid-feedback', 3);
        $this->assertSelectorTextContains('.invalid-feedback', 'This value is not valid email address.');
    }

    public function testSendContactAjax()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact_ajax');
        $form = $crawler->selectButton('Submit')
            ->form([
                'contact[name]' => 'john',
                'contact[phone]' => '0606060606',
                'contact[email]' => 'test@test.com',
                'contact[message]' => 'This is a new message',
            ]);

        $client->submit($form);
        $this->assertSelectorTextContains('.form-error-message', 'This value is not valid email address.');
    }
}
