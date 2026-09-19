<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected EntityManagerInterface $em,
        protected UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route(path: '/api/auth/logout', name: 'api_auth_logout')]
    public function apiAuthLogout(): JsonResponse
    {
        return $this->json([
            'message' => 'User log out successfully.'
        ]);
    }

    #[Route(path: '/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function apiAuthLogin(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/api/logout', name: 'api_logout')]
    public function apiLogout(): JsonResponse
    {
        return $this->json([
            'message' => 'User log out successfully.'
        ]);
    }

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(#[CurrentUser] ?User $user): JsonResponse
    {
//        $user = new User();
//        $user->setEmail('test@test.com');
//        $hashedPassword = $this->passwordHasher->hashPassword(
//            $user,
//            'password'
//        );
//        $user->setPassword($hashedPassword);
//        $this->em->persist($user);
//        $this->em->flush();
//        $user = $this->userRepository->findOneBy([
//            'email' => $request->request->get('username')
//        ]);
//
//        if (!$this->passwordHasher->isPasswordValid($user, $request->request->get('password'))) {
//            return $this->json([
//                'error' => 'Invalid credentials'
//            ], 400);
//        }

        if (null === $user) {
            return $this->json([
                'error' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
