<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\ArticleFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// define order load other fixtures
class AppFixtures extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $em) {/* silence is feature */}

    public function getDependencies() {

        return [
            ArticleFixtures::class ,
            UserFixtures::class ,
        ] ;
    }
}
