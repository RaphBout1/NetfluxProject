<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    // /**
    //  * @return Series[] Returns an array of Series objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    // /**
    //  * @return Query
    //  */
    
    public function findNotesCroissantes() 
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.noteUser > 0')
            ->orderBy('s.noteUser', 'DESC')
            ->getQuery()
            
        ;
    }

    // /**
    //  * @return Query
    //  */
    
    public function findOneByName($value) 
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title LIKE :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            
        ;
    }
    
}
