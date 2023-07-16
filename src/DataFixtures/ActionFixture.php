<?php

namespace App\DataFixtures;

use App\Entity\Game\Action;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ActionFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $action = new Action();
        $action->setType('use');
        $action->setDescription('You use the sword.');
        $action->setId(44);
        $action->setRequiredLocationId(11);
        $action->setItemId(99);
        $manager->persist($action);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
}
