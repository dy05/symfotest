<?php

namespace App\Tests\Subscriber;

use App\EventSubscriber\ExceptionSubscriber;
use Exception;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ExceptionSubscriberTest extends TestCase
{
    public function testEventSubscription()
    {
        $this->assertArrayHasKey(ExceptionEvent::class, ExceptionSubscriber::getSubscribedEvents() );
    }

    public function testOnExceptionSendEmail()
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send');

        $this->dispatch($mailer);
    }

    public function testOnExceptionSendFromGoodEmail()
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function (Email $message) {
                    return in_array(new Address('from@mailer.com'), $message->getFrom());
                })
            );

        $this->dispatch($mailer);
    }

    public function testOnExceptionSendToGoodEmail()
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function (Email $message) {
                    return in_array(new Address('to@mailer.com'), $message->getTo());
                })
            );

        $this->dispatch($mailer);
    }

    public function testOnExceptionSendGoodBody()
    {
        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function (Email $message) {
                    return str_contains($message->getHtmlBody(), ExceptionEvent::class)
                        && str_contains($message->getHtmlBody(), static::class)
                        && 'New Exception' === $message->getSubject();
                })
            );

        $this->dispatch($mailer);
    }

    /**
     * @param MockBuilder|MailerInterface $mailer
     * @return void
     */
    public function dispatch(mixed $mailer): void
    {
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $kernel->expects($this->never())
            ->method('boot');

        $event = new ExceptionEvent($kernel, new Request(), 1, new Exception());

        $subscriber = new ExceptionSubscriber($mailer, 'from@mailer.com', 'to@mailer.com');
//        $subscriber->onException($event);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }
}
