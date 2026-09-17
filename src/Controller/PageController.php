<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }

    #[Route('/main', name: 'app_auth')]
    #[IsGranted("IS_AUTHENTICATED")]
    public function auth(): Response
    {
        return $this->render('page/index.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/mail', name: 'app_mail')]
    #[IsGranted("IS_AUTHENTICATED")]
    public function mail(MailerInterface $mailer): Response
    {
        /** @var null|User $user */
        $user = $this->getUser();
        // Add try catch
        $mailer->send(
            new Email()->to('admin@test.com')
                ->from('no_reply@test.com')
                ->subject('New connexion' . ($user ? ' of ' . $user->getEmail() : ''))
                ->text('A user has been detected ' . ($user ? ' : id => ' . $user->getUserIdentifier() : ''))
        );

        return $this->render('page/index.html.twig');
    }

    #[Route('/admin', name: 'app_admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function admin(): Response
    {
        return $this->render('page/index.html.twig');
    }
}
