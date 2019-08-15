<?php

namespace App\Repository;

use App\Entity\OrganizationDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrganizationDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationDetail[]    findAll()
 * @method OrganizationDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrganizationDetail::class);
    }

    // /**
    //  * @return OrganizationDetail[] Returns an array of OrganizationDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrganizationDetail
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
