<?php

namespace App\DataFixtures;

use App\Entity\Game\Connection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ConnectionFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $connection = new Connection();
        $connection->setFromLocationId(11);
        $connection->setToLocationId(22);
        $connection->setId(1);
        $connection->setDirection('north');
        $manager->persist($connection);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
