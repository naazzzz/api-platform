<?php

namespace App\Repository;

use App\Entity\UserCar;
use App\Entity\UsersItemsInTheCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCar>
 *
 * @method UserCar|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCar|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCar[]    findAll()
 * @method UserCar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersCarsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,public UsersItemsInTheCarRepository $repository)
    {
        parent::__construct($registry, UserCar::class);
    }

    public function save(UserCar $entity, bool $flush = false): void
    {
        if($entity->itemsInTheCar->isEmpty()) {
        $items = new UsersItemsInTheCar();
        $this->repository->save($items, true);
        $entity->addItemsInTheCar($items);
    }
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UsersCars[] Returns an array of UsersCars objects
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

//    public function findOneBySomeField($value): ?UsersCars
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
