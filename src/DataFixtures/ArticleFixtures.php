<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

    private $userRep ;

    public function __construct( UserRepository $userRep ) {

        $this->userRep = $userRep ;
    }

    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR') ;

        for( $i = 0 , $size = \mt_rand( 10 , 35 ) ; $i < $size ; $i++ ) {

            $article = new Article( Article::FACTORY ) ;

            $article
                ->setTitle( $faker->words( 2 , true ) )
                ->setContent( $faker->sentence( 200 , true ) )
            ;

            if( \mt_rand( 0 , 10 ) < 3 ) {

                $article->setIsPublic( false ) ;
            }

            if( \mt_rand( 0 , 10 ) < 3 ) {

                $article->setIsRemove( false ) ;
            }

            if( \mt_rand( 0 , 10 ) < 3 ) {

                $article->setIsWarningPublic( true ) ;
            }

            $users = $this->userRep->getAllValid( 100 ) ;

            $indexUser = \mt_rand( 0 , ( \count( $users ) - 1 ) ) ;

            $user = $users[ $indexUser ] ;

            $article->setUser( $users[ $indexUser ] ) ;

            $em->persist( $article ) ;
        }

        $em->flush();
    }

    /**
     * @see DependentFixtureInterface
     */
    public function getDependencies() {

        return [
            UserFixtures::class
        ] ;
    }
}
