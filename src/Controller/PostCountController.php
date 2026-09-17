<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class PostCountController
{
    public function __construct(
        protected PostRepository $postRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function __invoke(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        $isOnline = $request->query->get('isOnline');

        $criteria = [];
        if ($isOnline !== null) {
            $criteria['online'] = filter_var($isOnline, FILTER_VALIDATE_BOOLEAN);
        }

        return $this->postRepository->count($criteria);
    }
}
