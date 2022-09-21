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
 * @extends ServiceEntityRepository<Organization>
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function findOneByTitle(string $title): ?Organization
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.proposedOrganizationDetails', 'opd')
            ->andWhere('opd.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Organization[]
     */
    public function findAllWithProposedDetails(bool $hasChanges, bool $notApproved): array
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.proposedOrganizationDetails', 'opd')
            ->addSelect('opd')
            ->orderBy('opd.title');

        if ($hasChanges) {
            $qb->andWhere('o.proposedOrganizationDetails != o.acceptedOrganizationDetails');
        }

        if ($notApproved) {
            $qb->andWhere('o.acceptedOrganizationDetails IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    public function countOrganizationsWithSessions(?bool $cancelled)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('count(distinct o.id)')
            ->innerJoin('o.sessions', 's')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL');

        if ($cancelled !== null) {
            $qb->andWhere('s.cancelled = :cancelled')->setParameter('cancelled', $cancelled);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Organization[]
     */
    public function findOrganizationsAwaitingMailNotification()
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'u')
            ->innerJoin('o.owner', 'u')
            ->andWhere('o.sendBatchMailNotification = TRUE')
            ->getQuery()
            ->getResult();
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
