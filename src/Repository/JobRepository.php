<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @param User $user
     * @return Job[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('j')
            ->innerJoin('j.organization', 'o')
            ->andWhere('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult();
    }

    public function findAllWithProposedDetails()
    {
        return $this->createQueryBuilder('j')
            ->innerJoin('j.proposedDetails', 'jdp')
            ->addSelect('jdp')
            ->orderBy('jdp.title')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Job[]
     */
    public function findFullyAccepted(): array
    {
        $qb = $this->createQueryBuilder('j')
            ->innerJoin('j.acceptedDetails', 'jad')
            ->addSelect('jad')
            ->innerJoin('j.organization', 'o')
            ->addSelect('o')
            ->innerJoin('o.acceptedOrganizationDetails', 'oad')
            ->addSelect('oad')
            ->andWhere('j.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL');

        return $qb->getQuery()->getResult();
    }
}
