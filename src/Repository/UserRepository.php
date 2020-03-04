<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createStaticQuery( string $constraint , ?int $limit ) {

        $query = $this
            ->createQueryBuilder('u')
            ->andWhere( $constraint )
        ;

        if( is_int( $limit ) && $limit > 0 ) {

            $query->setMaxResults( $limit ) ;
        }

        return $query ;
    }

    public function getAllRemoves( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isRemove = true' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllVisible( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isRemove = false' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllPublicEmail( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isPublicEmail = true' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllPublicProfil( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isPublicProfil = true' , $limit )
            ->getQuery()
            ->getResult()
        ;

    }

    public function getAllValid( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isValid = true' , $limit )
            ->getQuery()
            ->getResult()
        ;

    }

    public function getAllInvalid( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isValid = false' , $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllPrivateEmail( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isPublicEmail = false' , $limit )
            ->getQuery()
            ->getResult()
        ;

    }

    public function getAllPrivateProfil( ?int $limit ) {

        return $this
            ->createStaticQuery( 'u.isPublicProfil = false' , $limit )
            ->getQuery()
            ->getResult()
        ;

    }

    public function getAllVisibleSearch( string $search ) {

        $users = $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :username')
            ->andWhere('u.isRemove = false')
            ->andWhere('u.isPublicProfil = true')
            ->setParameter('username', '%'.$search.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        $usersID = [] ;

        foreach( $users as $user ) {

            $usersID[] = $user->getId() ;
        }

        return $usersID ;
    }
}
