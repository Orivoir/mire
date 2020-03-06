<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Commentary;
use App\Repository\UserRepository;
use App\DataFixtures\ArticleFixtures;
use App\Repository\ArticleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentaryFixtures extends Fixture implements DependentFixtureInterface
{

    private $articleRep ;
    private $userRep ;

    public function __construct(
        ArticleRepository $articleRep ,
        UserRepository $userRep
    ) {

        $this->articleRep = $articleRep ;
        $this->userRep = $userRep ;
    }

    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR') ;

        for( $i = 0 , $size = \mt_rand( 30,65 ) ; $i < $size ; $i++ ) {

            $commentary = new Commentary();

            $commentary->setContent( $faker->sentence( 15 , true ) ) ;

            $articles = $this->articleRep->getAllVisible( 1e3 ) ;

            $indexArticle = \mt_rand( 0 , \count( $articles ) - 1 ) ;

            $article = $articles[ $indexArticle ] ;

            $commentary->setArticle( $article ) ;

            $users = $this->userRep->getAllValid( 1e3 ) ;

            $indexUser = \mt_rand( 0 , \count( $users ) - 1 ) ;

            $user = $users[ $indexUser ] ;

            $commentary->setUser( $user ) ;

            if( \mt_rand( 0 , 10 ) < 3  ) {

                $commentary->setIsRemove( true ) ;
            }

            $em->persist( $commentary );
        }

        $em->flush();
    }

    /**
     * @see DependentFixtureInterface
     */
    public function getDependencies() {

        return [
            ArticleFixtures::class
        ] ;
    }
}
