<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Contact;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{

    private $userRep ;

    public function __construct( UserRepository $userRep ) {

        $this->userRep = $userRep ;
    }

    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR') ;

        for( $i = 0 , $size = \mt_rand( 9 , 42 ) ; $i < $size ; $i++ ) {

            $contact = new Contact( Contact::FACTORY );

            $contact->setTitle( $faker->sentence( 4 , true ) ) ;
            $contact->setContent( $faker->sentence( 15 , true ) ) ;

            if( \mt_rand( 0 , 10 ) >= 5 ) {

                $users = $this->userRep->findAll() ;

                $indexUser = \mt_rand( 0 , \count( $users ) - 1 ) ;

                $user = $users[ $indexUser ] ;

                $contact->setUser( $user ) ;
            }

            $em->persist($contact);
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
