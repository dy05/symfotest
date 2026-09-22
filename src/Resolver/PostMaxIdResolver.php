<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\Book;
use App\Repository\PostRepository;

final class PostMaxIdResolver implements QueryCollectionResolverInterface
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    /**
     * @param iterable<Book> $collection
     *
     * @return iterable<Book>
     */
    public function __invoke(iterable $collection, array $context): iterable
    {
        $maxId = $context['args']['id'] ?? null;

        $qb = $this->postRepository->createQueryBuilder('p');

        if (null !== $maxId) {
            $qb->andWhere('p.id <= :maxId')->setParameter('maxId', $maxId);
        // } else {
        //     $globalMax = $this->postRepository->createQueryBuilder('p2')
        //         ->select('MAX(p2.id)')
        //         ->getQuery()
        //         ->getSingleScalarResult();
        //     $qb->andWhere('p.id = :maxId')->setParameter('maxId', $globalMax);
        }

        return $qb->getQuery()->getResult();
    }
}
