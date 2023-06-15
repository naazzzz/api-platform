<?php

namespace App\Repository;

use App\Entity\UsersItemsInTheCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsersItemsInTheCar>
 *
 * @method UsersItemsInTheCar|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersItemsInTheCar|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersItemsInTheCar[]    findAll()
 * @method UsersItemsInTheCar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersItemsInTheCarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersItemsInTheCar::class);
    }

    public function save(UsersItemsInTheCar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UsersItemsInTheCar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UsersItemsInTheCar[] Returns an array of UsersItemsInTheCar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UsersItemsInTheCar
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
