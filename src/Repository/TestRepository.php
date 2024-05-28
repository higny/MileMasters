<?php

namespace App\Repository;

use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Test>
 *
 * @method Test|null find($id, $lockMode = null, $lockVersion = null)
 * @method Test|null findOneBy(array $criteria, array $orderBy = null)
 * @method Test[]    findAll()
 * @method Test[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

   /**
    * @return Test[] Returns an array of Test objects
    */
    public function findAllWithEmptyTend(): array
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.entries', 'e')
            ->where('e.Tend IS NULL')
            ->getQuery()
            ->getResult();
    }

     
    
    public function getSummary()
    {
        return $this->createQueryBuilder('t')
            ->select(
                't.Name',
                'COUNT(e.id) AS totalEntries',
                'SUM(CASE WHEN e.Tend IS NOT NULL AND e.Rw = true THEN 1 ELSE 0 END) AS runners',
                'SUM(CASE WHEN e.Tend IS NOT NULL AND e.Rw = false THEN 1 ELSE 0 END) AS walkers',
                'SUM(CASE WHEN e.Tend IS NULL THEN 1 ELSE 0 END) AS abstentions'
            )
            ->leftJoin('t.entries', 'e')
            ->groupBy('t.id')
            ->orderBy('t.Date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
