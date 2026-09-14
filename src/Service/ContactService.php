<?php

namespace App\Service;

use App\Data\ContactData;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ContactService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $em,
        private Environment $twig,
        private string $contactRecipient = 'admin@test.com',
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function handleForm(ContactData $data): void
    {
        $email = new Email()
//            ->from($data->email)
            ->from('no_reply@test.com')
            ->replyTo($data->email)
            ->to($this->contactRecipient)
            ->subject('New contact submission')
            ->html($this->buildBody($data))
//            ->text($this->buildBody($data))
        ;

        $this->mailer->send($email);
        $this->em->persist(Contact::fromForm($data));
        $this->em->flush();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function buildBody(ContactData $data): string
    {
//        return sprintf(
////            "New contact form submission\n\nName: %s\nEmail: %s\nSubject: %s\n\nMessage:\n%s",
//            "New contact form submission\n\nName: %s\nEmail: %s\nPhone: %s\n\nMessage:\n%s",
//            $data->name,
//            $data->email,
////            $data->subject,
//            $data->phone,
//            $data->message
//        );

        return $this->twig->render('emails/contact.html.twig', [
            'data' => $data
        ]);
    }
}
