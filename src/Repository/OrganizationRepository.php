<?php

namespace App\Repository;

use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function findOneByTitle($title)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.proposedOrganizationDetails', 'opd')
            ->andWhere('opd.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllWithProposedDetails(): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.proposedOrganizationDetails', 'opd')
            ->addSelect('opd')
            ->orderBy('opd.title')
            ->getQuery()
            ->getResult();
    }
}
