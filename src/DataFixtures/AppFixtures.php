<?php

namespace App\DataFixtures;

use App\Entity\Dynamicqcm;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->fixtureForQcm($manager);
        $this->fixtureForUser($manager);
    }
    public function fixtureForQcm(ObjectManager $manager): void
    {
        $dynamicqcm = new Dynamicqcm();
        $dynamicqcm->setTheme('SCRUM_AGILITE');
        $dynamicqcm->setNbrQuestion(10);
        $manager->persist($dynamicqcm);
        $manager->flush();
    }
    public function fixtureForUser(ObjectManager $manager): void
    {
        $user = new User(
            45,
            'titouan@gmail.com',
            'azerty',
            'Titouan',
            'Vernagut',
            );
        $manager->persist($user);
        $manager->flush();
    }
}
