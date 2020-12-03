<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

   /**
    * @return Annonces[] Returns an array of Annonces objects
    */
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
    /**
    * Recherche par mot clés
    * @param [type] $mots
    * @return void
    */
    public function search($mots = null, $categorie = null) {
        $query = $this->createQueryBuilder('a');
        $query->where('a.active = 1');
        if($mots != null) {
            $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST(:mots boolean)>0')->setParameter('mots', $mots);
        }
        if( $categorie != null){
            $query->leftJoin('a.categories','c');
            $query->andWhere('c.id = :id')->setParameter('id', $categorie);
        }

        return $query->getQuery()->getResult();
    }

    /**
    * returns numbre Annonces of one day
    * 
    */
    public function countByDate(){
        // première méthode avec le querybuilder

        /* $query = $this->createQueryBuilder('a')
        ->select('SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count ')
        ->groupBy('dateAnnonces')
        ;
        return $query->getQuery()->getResult(); */

        // autre méthode en sql sans queryBuilder

        $query = $this->getEntityManager()->createQuery("
            select substring(a.created_at,1,10) as dateAnnonces, count(a) as count from App\Entity\Annonces a group by dateAnnonces
        ");
        return $query->getResult();
    }


    /**
     * returns annonces entre deux jours
     */
    public function selectInterval($from, $to, $categ = null) {
        // d'abord sans le public function findByExampleField($value)
        /* $query = $this->getEntityManager()->createQuery("
            SELECT a FROM App\Entity\Annonces a WHERE a.created_at > :from AND a.created_at < :to
        ")
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
        return $query->getResult(); */


        //query builder
        $query = $this->createQueryBuilder('a')
            ->where('a.created_at > :from')
            ->andwhere('a.created_at < :to');

            if( $categ != null ){
                $query->leftJoin('a.categories','c')
                 ->andWhere('c.id = :categ')
                 ->setParameter(':categ', $categ);
            }
            return $query->getQuery()->getResult();
    }
    
    /**
     * Returns all Annonces per page
     * @IsGranted("ROLE_ADMIN")
     */
    public function getPaginatedAnnonces($page, $limit)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.active = 1')
            ->orderBy('a.created_at')
            ->setFirstResult(($page*$limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }



    /**
     $ returns total  annnonces
     * @IsGranted("ROLE_ADMIN")
     */
    public function getTotalAnnonces()
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.active = 1')

        ;

        return $query->getQuery()->getSingleScalarResult();
    }
//     */

//     /*
//     public function findOneBySomeField($value): ?Annonces
//     {
//         return $this->createQueryBuilder('a')
//             ->andWhere('a.exampleField = :val')
//             ->setParameter('val', $value)
//             ->getQuery()
//             ->getOneOrNullResult()
//         ;
//     }
//     */
}
