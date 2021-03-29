<?php

namespace App\Repository;

use App\Entity\JobDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobDetail[]    findAll()
 * @method JobDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobDetail::class);
    }
}
