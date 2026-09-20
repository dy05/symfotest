<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
//use App\Entity\User;
//use App\Contract\UserOwnedInterface;
//use Symfony\Bundle\SecurityBundle\Security;
use Throwable;

final class UserOwnerStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $persistProcessor, // decorated: api_platform.doctrine.orm.state.persist_processor
//        private readonly Security $security,
    ) {
    }


    /**
     * Handles the state.
     *
     * @param mixed $data
     * @param Operation $operation
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return mixed
     * @throws Throwable
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
//        if ($data instanceof UserOwnedInterface) {
//            $user = $this->security->getUser();
//
//            if ($user instanceof User) {
//                $data->setUser($user);
//            }
//        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
