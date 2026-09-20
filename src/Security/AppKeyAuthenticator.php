<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppKeyAuthenticator extends AbstractAuthenticator
{
    private string $authorizationKey = '';
    private User $user;

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function supports(Request $request): bool
    {
        $authorization = $request->headers->get('authorization');
        $this->authorizationKey = str_replace('Bearer ', '', $authorization ?? '');
        return ! empty($authorization) && str_starts_with($authorization, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $user = $this->userRepository->findOneBy(['apiKey' => $this->authorizationKey]);

        if (! $user || $user->getApiKey() !== $this->authorizationKey) {
            throw new AuthenticationCredentialsNotFoundException('Invalid apiKey');
        }

        $this->user = $user;
        return new Passport(
            new UserBadge($user->getUserIdentifier()),
            new CustomCredentials(fn () => true, [])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $this->user;

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
//            'error' => $exception->getMessage(),
            'message' => $exception->getMessage(),
        ], Response::HTTP_FORBIDDEN);
    }
}
