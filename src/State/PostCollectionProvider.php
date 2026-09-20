<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class PostCollectionProvider implements ProviderInterface
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return object|array|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $this->postRepository->findBy(['user' => $user]);
        }

        return $this->postRepository->findBy(['user' => null]);
    }
}
