<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Session::class);
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
     * @return Session[]
     */
    public function findFullyAccepted(): array
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
            ->getQuery()
            ->getResult();
    }
}
