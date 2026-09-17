<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\DependencyRepository;

class DependencyStateProcessor implements ProcessorInterface
{
    public function __construct(protected DependencyRepository $dependencyRepository) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->dependencyRepository->delete($data);
        } else {
            $this->dependencyRepository->persist($data);
        }

        return $data;
    }
}
