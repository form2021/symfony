<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    // on se crée une méthode à nous pour effectuer une requête spéciale
    // https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
    public function chercherMot ($mot)
    {
        $entityManager = $this->getEntityManager();

        // ATTENTION: REQUETE EN DQL (Doctrine Query Language)
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Annonce a
            WHERE a.titre LIKE :titre
            ORDER BY a.datePublication DESC'
        )
        ->setParameter('titre', "%$mot%");
        // on rajoute les % pour chercher un titre qui contient le mot
        // https://sql.sh/cours/where/like

        return $query->getResult();

    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
