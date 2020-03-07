<?php

namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\Message;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRep ;

    public function __construct(
        UserRepository $userRep
    ) {
        $this->userRep = $userRep ;
    }


    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR') ;

        for( $i=0,$size= \mt_rand(50,160); $i < $size; $i++) {

            $message = new Message( Message::FACTORY );

            $message->setContent( $faker->sentence( 10 , true ) ) ;

            if( \mt_rand( 0 ,10 ) >= 5 ) {

                $message->setTitle( $faker->sentence( 3 , true ) ) ;
            }

            $usersValid = $this->userRep->getAllValid( 1e3 ) ;

            $indexTarget = \mt_rand( 0, \count( $usersValid ) - 1 ) ;

            do {

                $indexAuthor = \mt_rand( 0, \count( $usersValid ) - 1 ) ;

            } while( $indexAuthor == $indexTarget ) ;

            $message->setAuthor( $usersValid[ $indexAuthor ] ) ;
            $message->setBox( $usersValid[ $indexTarget ]->getMessageBox() ) ;

            if( \mt_rand( 0,10 ) >= 6 ) {

                $message->setIsRead( true ) ;
            }

            if( \mt_rand( 0,10 ) >= 7 ) {

                $message->setIsRemove( true ) ;
            }

            $em->persist( $message );
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
