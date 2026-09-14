<?php

namespace App\EventSubscriber;

use App\Entity\Contact;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

readonly class ContactMailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [];
    }

    public function sendMail(ViewEvent $event): void
    {
        $contact = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$contact instanceof Contact || Request::METHOD_POST !== $method) {
            return;
        }

        $message = new Email()
            ->from($contact->getEmail())
            ->to('admin@test.com')
            ->subject('New Exception')
            ->html($this->twig->render('emails/contact.html.twig', ['data' => $contact]));

        $this->mailer->send($message);
    }
}
