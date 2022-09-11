<?php

namespace App\Repository;

use App\Entity\Apprenticeship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apprenticeship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apprenticeship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apprenticeship[]    findAll()
 * @method Apprenticeship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprenticeshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apprenticeship::class);
    }

    /**
     * @return Apprenticeship[]
     */
    public function findAllWithProposedDetails(bool $hasChanges, bool $notApproved): array
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.proposedDetails', 'apd')
            ->addSelect('apd');

        if ($hasChanges) {
            $qb->andWhere('a.proposedDetails != a.acceptedDetails');
        }

        if ($notApproved) {
            $qb->andWhere('a.acceptedDetails IS NULL');
        }

        return $qb->getQuery()->getResult();
    }
}
