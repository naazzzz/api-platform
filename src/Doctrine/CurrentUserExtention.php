<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserCar;
use App\Entity\UsersItemsInTheCar;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class CurrentUserExtention implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        switch ($resourceClass){

            case UserCar::class:
                $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
                $queryBuilder->setParameter('current_user', $user->getId());
                break;
            case User::class:
                $queryBuilder->andWhere(sprintf('%s.id = :current_user', $rootAlias));
                $queryBuilder->setParameter('current_user', $user->getId());
                break;
            case UsersItemsInTheCar::class:
                $queryBuilder->innerJoin(UserCar::class,'u',Join::WITH,'u.id='.$rootAlias.'.car');
                $queryBuilder->andWhere(sprintf('%s.user = :current_user', 'u'));
                $queryBuilder->setParameter('current_user', $user->getId());
                break;

            default:
                return;
        }


    }

}