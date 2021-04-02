<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function resetAllStartAndEndTimes(): void
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->update()
            ->set('s.start', 'null')
            ->set('s.stop', 'null')
            ->getQuery()
            ->execute();
    }

    /**
     * @param User $user
     * @return Session[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.organization', 'o')
            ->andWhere('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param bool $excludeCancelled
     * @return Session[]
     */
    public function findFullyAccepted($excludeCancelled = false): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->addSelect('sad')
            ->innerJoin('s.organization', 'o')
            ->addSelect('o')
            ->innerJoin('o.acceptedOrganizationDetails', 'oad')
            ->addSelect('oad')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->orderBy('s.start', 'ASC');

        if ($excludeCancelled) {
            $qb->andWhere('s.cancelled = FALSE');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Session[]
     */
    public function findAllWithProposedDetails(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.proposedDetails', 'sdp')
            ->addSelect('sdp')
            ->orderBy('s.start')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Session[]
     */
    public function findRecentlyApprovedSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->addSelect('sad')
            ->innerJoin('s.organization', 'o')
            ->addSelect('o')
            ->innerJoin('o.acceptedOrganizationDetails', 'oad')
            ->addSelect('oad')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->andWhere('s.acceptedAt IS NOT NULL')
            ->andWhere('s.start > CURRENT_TIMESTAMP()')
            ->andWhere('s.cancelled = FALSE')
            ->orderBy('s.acceptedAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
