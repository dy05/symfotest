<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use App\Contract\UserOwnedInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        try {
            $reflectionClass = new ReflectionClass($resourceClass);
            if (!$reflectionClass->implementsInterface(UserOwnedInterface::class)) {
                return;
            }
        } catch (Exception $exc) {
            if ($exc->getCode()) {
                return;
            }
        }

        return;
        /** @var User $user */
        $user = $this->security->getUser();
        $alias = $queryBuilder->getRootAliases()[0];
        if (empty($alias)) {
            return;
        }

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return;
            }

            $queryBuilder->andWhere(sprintf('%s.user = :current_user', $alias));
            $queryBuilder->setParameter('current_user', $user);
        } else {
            $queryBuilder->andWhere(sprintf('%s.user IS NULL', $alias));
        }
    }

//    public function supportsResult(string $resourceClass, ?Operation $operation = null, array $context = []): bool
//    {
//        return $resourceClass === Post::class;
//    }

//    public function getResult(QueryBuilder $queryBuilder, ?string $resourceClass = null, ?Operation $operation = null, array $context = []): ?object
//    {
//        $alias = $queryBuilder->getRootAliases()[0];
//        /** @var User $user */
//        $user = $this->security->getUser();
//        if ($user) {
//            $queryBuilder->andWhere($alias . '.user = :current_user')
//                ->setParameter('current_user', $user->getId());
//        } else {
//            $queryBuilder->andWhere($alias . '.user IS NULL');
//        }
//
//        return $queryBuilder;
//    }
}
