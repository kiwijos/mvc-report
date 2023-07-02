<?php

namespace App\DataFixtures;

use App\Entity\Game\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ItemFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $item = new Item();
        $item->setName('sword');
        $item->setDescription('A powerful sword.');
        $item->setId(99);
        $item->setLocationId(11);
        $manager->persist($item);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
