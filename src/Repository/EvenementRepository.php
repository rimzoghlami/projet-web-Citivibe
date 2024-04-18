<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//Query Builder: Question 1
public function showAllEvenementsOrderByCategorie()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.categoriee','DESC')
            ->getQuery()
            ->getResult()
        ;
    }

/*
function SearchEvenementDQL($min,$max){
    $em=$this->getEntityManager();
    return $em->createQuery(
        'select a from App\Entity\Evenement a WHERE 
        a.nb_books BETWEEN ?1 AND ?2')
        ->setParameter(1,$min)
        ->setParameter(2,$max)->getResult();
}
function DeleteEvenement(){
    $em=$this->getEntityManager();
    return $em->createQuery(
        'DELETE App\Entity\Evenement a WHERE a.nb_books = 0')
    ->getResult();
}*/
}
