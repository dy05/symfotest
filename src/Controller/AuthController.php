<?php

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class AuthController
{
    public function __construct(
        protected Security $security
    ) {
    }

    public function __invoke(): null|UserInterface|User
    {
        return $this->security->getUser();
    }
}
