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
    public function testSendContactWithInvalidPhonePanther()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('Submit')
            ->form([
                'name' => 'test',
                'phone' => '06060606',
                'email' => 'test@test.com',
                'message' => 'This is a new message',
            ]);

        $client->getWebDriver()
            ->findElement(WebDriverBy::name('rgpd'))
            ->click();
        $client->submit($form);

        $client->waitFor('.invalid-feedback', 1);
        $this->assertSelectorTextContains('.invalid-feedback', 'This value should have exactly 10 characters.');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testValidAjaxSendContact()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('Submit')
            ->form([
                'name' => 'john',
                'phone' => '0606060606',
                'email' => 'test@test.com',
                'message' => 'This is a new message',
            ]);

        $client->getWebDriver()
            ->findElement(WebDriverBy::name('rgpd'))
            ->click();
        $client->submit($form);
        $client->waitFor('.alert', 1);
        $this->assertSelectorTextContains('.alert.alert-success', 'Your mail successfully sent.');
    }
}
