<?php

namespace App\Repository;

use App\Entity\SessionDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionDetail[]    findAll()
 * @method SessionDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionDetail::class);
    }
}
