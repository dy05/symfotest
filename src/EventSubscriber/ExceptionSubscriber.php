<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string          $from,
        private string          $to
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onException',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onException(ExceptionEvent $event): void
    {
        $message = new Email()
            ->from($this->from)
            ->to($this->to)
            ->subject('New Exception')
            ->html(
                get_class($event) . " has exception <br/>"
                . $event->getThrowable()->getTraceAsString() . " => <br/>"
                . $event->getThrowable()->getMessage()
            );

        $this->mailer->send($message);
    }
}
