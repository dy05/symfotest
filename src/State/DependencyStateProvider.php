<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

class DependencyStateProvider implements ProviderInterface
{
    public function __construct(protected DependencyRepository $dependencyRepository) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Dependency|array|null
    {
        $data = $this->dependencyRepository->findAll();

        if ($operation instanceof CollectionOperationInterface) {
            return $data;
        }

        return $data[$uriVariables['uuid']] ?? null;
    }
}
