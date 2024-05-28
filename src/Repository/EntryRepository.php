<?php

namespace App\Repository;

use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entry>
 *
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    /**
    * @return ?int Retourne le dernier N° Dossard utilisé
    */
    public function findLastNoDos(): ?int
    {
        $result = $this->createQueryBuilder('e')
            ->select('e.NoDos')
            ->orderBy('e.NoDos', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        
        return $result !== null ? (int) $result['NoDos'] : null;
    }

   /**
    * @return int Retourne le prochain N° Dossard
    */
    public function getNextNoDos(): int
    {
        return ($this->findLastNoDos() ?? 0) + 1;
    }

    public function findEntriesByTestIdWithNullTendAndStudent($testId)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.Test', 't')
            ->innerJoin('e.student', 's')
            ->addSelect('e.id, CONCAT(\'N° \', e.NoDos, \' - \', s.Prenom, \' \', s.Nom) AS displayValue')
            ->where('t.id = :testId')
            ->andWhere('e.Tend IS NULL')
            ->setParameter('testId', $testId)
            ->getQuery()
            ->getResult();
    }

    public function findEntriesWithTendAndOrderedByTemps($testId, $rw)
    {
        $results = $this->createQueryBuilder('e')
        ->innerJoin('e.Test', 't')
        ->innerJoin('e.student', 's')
        ->addSelect('e.id, CONCAT(\'N° \', e.NoDos, \' - \', s.Prenom, \' \', s.Nom) AS displayValue, e.Temps')
        ->where('e.Tend IS NOT NULL')
        ->andWhere('e.Rw = :rw')
        ->andWhere('t.id = :testId')
        ->orderBy('e.Temps', 'ASC')
        ->setParameter('rw', $rw)
        ->setParameter('testId', $testId)
        ->getQuery()
        ->getResult();

        foreach ($results as &$result) {
            $hours = $result['Temps']->h + ($result['Temps']->d * 24);
            $minutes = $result['Temps']->i;
            $result['formattedTemps'] = sprintf('%02d Heures et %02d minutes', $hours, $minutes);
        }

        return $results;
    }
}
