<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function getSeriesByPopularity(float $popularity): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.popularity', 'DESC')
            ->where('s.popularity >= :popularity')
            ->orWhere("s.name LIKE '%z%'")
            ->andWhere('s.firstAirDate >= :firstAirDate')
            ->setParameter('popularity', $popularity)
            ->setParameter('firstAirDate', new \DateTime('2017-01-01'))
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getSeriesWithDql(float $popularity): array
    {
        $dql = "SELECT s FROM App\Entity\Serie s 
            WHERE s.popularity >= :popularity
            AND s.firstAirDate >= :firstAirDate
            OR s.name LIKE '%z%'
            ORDER BY s.popularity DESC";

         return $this->getEntityManager()->createQuery($dql)
             ->setParameter('popularity', $popularity)
             ->setParameter('firstAirDate', new \DateTime('2017-01-01'))
             ->setMaxResults(5)
             ->execute();
    }


    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
