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

public function ExpiredDate(Evenenent $evenement)
{
    $now = new \DateTime();
    if ($evenement->getDate() < $now) {
        throw new \Exception('The challenge date has already expired.');
    }

    $this->_em->persist($evenement);
    $this->_em->flush();
}
public function isEventNotExpired(Evenement $evenement): bool
{
    $now = new \DateTime();
    return $evenement->getDate() >= $now;
}
// Dans votre repository EvenementRepository
public function findNonExpiredEvents(): array
{
    $now = new \DateTime();
    return $this->createQueryBuilder('e')
        ->andWhere('e.date >= :now')
        ->setParameter('now', $now)
        ->getQuery()
        ->getResult();
}
public function findExpiredEvents(): array
{
    $now = new \DateTime();
    return $this->createQueryBuilder('e')
        ->andWhere('e.date < :now')
        ->setParameter('now', $now)
        ->getQuery()
        ->getResult();
}
public function searchByNom($searchTerm)
{
    return $this->createQueryBuilder('e')
        ->andWhere('e.nome LIKE :searchTerm')
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->getQuery()
        ->getResult();
}
public function trierParCategorie(): array
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.categoriee', 'ASC') // ou 'DESC' si vous voulez trier en ordre décroissant
        ->getQuery()
        ->getResult();
}
public function findByCategoriepAndNomContains(string $categoriee, ?string $nome): array
{
    $qb = $this->createQueryBuilder('p')
        ->andWhere('p.categoriee = :categoriee')
        ->setParameter('categoriee', $categoriee);

    // Filtrer uniquement sur les événements non expirés
    $now = new \DateTime();
    $qb->andWhere('p.date >= :now')
        ->setParameter('now', $now);

    if (!empty($nome)) {
        $qb->andWhere('p.nome LIKE :nome')
            ->setParameter('nome', '%'.$nome.'%');
    }

    return $qb->getQuery()->getResult();
}
public function trierParNom($order = 'ASC'): array
{
    $queryBuilder = $this->createQueryBuilder('p');
    $queryBuilder
        ->andWhere('p.date >= :now')
        ->setParameter('now', new \DateTime());

    $queryBuilder->orderBy('p.nome', $order);
    return $queryBuilder->getQuery()->getResult();
}

public function categoryExists($category)
    {
        $entityManager = $this->getEntityManager();

        // Construire la requête pour vérifier si la catégorie existe
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Evenement p
            WHERE p.categoriee = :category'
        )->setParameter('category', $category);

        // Exécuter la requête et récupérer les résultats
        $result = $query->getResult();

        // Si des résultats sont trouvés, la catégorie existe
        return !empty($result);
    }
    public function getEventCountsPerCategory(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.categoriee AS categoryId, COUNT(e) AS eventCount')
            ->groupBy('e.categoriee')
            ->getQuery()
            ->getResult();
    }
    
}
