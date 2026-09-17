<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AuthController
{
    public function __construct(
        protected Security $security
    ) {
    }

    public function __invoke(): ?User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return $user;
    }
}
