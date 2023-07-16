<?php

namespace App\DataFixtures;

use App\Entity\Game\Response;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ResponseFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $response = new Response();
        $response->setType('move');
        $response->setId(55);
        $response->setActionId(44);
        $response->setLocationId(22);
        $manager->persist($response);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
