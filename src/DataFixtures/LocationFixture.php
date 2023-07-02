<?php

namespace App\DataFixtures;

use App\Entity\Game\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class LocationFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $location1 = new Location();
        $location1->setName('Cave');
        $location1->setDescription('A dark cave.');
        $location1->setDetails('The cave is damp and cold.');
        $location1->setId(11);
        $manager->persist($location1);

        $location2 = new Location();
        $location2->setName('Lake');
        $location2->setDescription('A serene lake.');
        $location2->setDetails('A calm lake reflecting the beauty of its surroundings.');
        $location2->setId(22);
        $manager->persist($location2);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
