<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Article;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $passwordEncoder ;

    public function __construct( UserPasswordEncoderInterface $passwordEncoder ) {

        $this->passwordEncoder = $passwordEncoder ;
    }

    public function load( ObjectManager $em ) {

        $faker = Factory::create('fr_FR') ;

        for( $i = 0 , $size = \mt_rand( 15 , 60 ) ; $i < $size ; $i++  ) {

            $user = new User( User::FACTORY ) ;

            $user->setPlainPassword("a") ;

            $user
                ->setUsername( $faker->userName )
                ->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        $user->getPlainPassword()
                    )
                )
            ;

            $isFname = \mt_rand( 1 , 10 ) >= 6 ;

            if( $isFname ) {
                $user->setFname( $faker->firstName ) ;
            }

            $isName = \mt_rand( 1 , 10 ) > 6 ;

            if( $isName ) {
                $user->setName( $faker->name ) ;
            }

            $isPublicEmail = \mt_rand( 1 , 10 ) >= 3 ;

            if( $isPublicEmail ) {

                $user->setIsPublicEmail( true ) ;
            }

            $isPublicProfil = \mt_rand( 1 , 10 ) >= 6 ;

            if( $isPublicProfil ) {

                $user->setIsPublicProfil( true ) ;
            }

            $isEmail = \mt_rand( 1 , 10 ) >= 5 ;

            if( $isEmail ) {

                $user->setEmail( $faker->email ) ;

                $isValidAccount = \mt_rand( 1 , 10 ) >= 5 ;

                if( $isValidAccount ) {

                    $user->setIsValid( true ) ;
                }
            }

            $isRemove = \mt_rand( 1 , 10 ) < 3 ;

            if( $isRemove ) {

                $user->setIsRemove( true ) ;
            }

            $em->persist( $user ) ;
        }

        $em->flush() ;
    }
}
