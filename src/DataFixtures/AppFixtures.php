<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $taldeaA1 = new Group();
        $taldeaA1->setName('A1');
        $manager->persist($taldeaA1);

        $taldeaA2 = new Group();
        $taldeaA2->setName('A2');
        $manager->persist($taldeaA2);

        $taldeaB1 = new Group();
        $taldeaB1->setName('B1');
        $manager->persist($taldeaB1);

        $taldeaB2 = new Group();
        $taldeaB2->setName('B2');
        $manager->persist($taldeaB2);

        $taldeaC1 = new Group();
        $taldeaC1->setName('C1');
        $manager->persist($taldeaC1);

        $taldeaC2 = new Group();
        $taldeaC2->setName('C2');
        $manager->persist($taldeaC2);

        $manager->flush();
    }
}
