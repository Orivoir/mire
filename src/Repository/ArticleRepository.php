<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
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
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createStaticQuery( string $constraint , ?int $limit ) {

        $query = $this
            ->createQueryBuilder('a')
            ->andWhere( $constraint )
        ;

        if( is_int( $limit ) && $limit > 0 ) {

            $query->setMaxResults( $limit ) ;
        }

        return $query ;
    }

    public function getAllRemoves( ?int $limit ) {

        return $this
            ->createStaticQuery( 'a.isRemove = true' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllVisible( ?int $limit ) {

        return $this
            ->createStaticQuery( 'a.isRemove = false' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllPublic( ?int $limit ) {

        return $this
            ->createStaticQuery( 'a.isPublic = true' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllWarning( ?int $limit ) {

        return $this
            ->createStaticQuery( 'a.isWarningPublic = true' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getLastPublish( int $limit = 3 ) {

        return $this
            ->createStaticQuery( 'a.isWarningPublic = false' , $limit )
            ->orderBy('a.createAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllVisibleSearch( string $search ) {

        $articles = $this->createQueryBuilder('a')
            ->orWhere('a.title LIKE :title')
            ->orWhere('a.content LIKE :content')
            ->setParameter('title', '%'.$search.'%')
            ->setParameter('content', '%'.$search.'%')
            ->andWhere('a.isRemove = false')
            ->orderBy('a.createAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;

        $articlesID = [] ;

        foreach( $articles as $article ) {

            $articlesID[] = $article->getId() ;
        }

        return $articlesID ;
    }

}
