<?php

namespace App\Repository;

use App\Entity\ApprenticeshipDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApprenticeshipDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprenticeshipDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprenticeshipDetail[]    findAll()
 * @method ApprenticeshipDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprenticeshipDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApprenticeshipDetail::class);
    }

    // /**
    //  * @return ApprenticeshipDetail[] Returns an array of ApprenticeshipDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApprenticeshipDetail
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
