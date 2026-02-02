<?php

namespace App\DataFixtures;

use App\Entity\Dynamicqcm;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->fixtureForQcm($manager);
        $this->fixtureForUser($manager);

        $manager->flush();
    }

    public function fixtureForQcm(ObjectManager $manager): void
    {
        $dynamicqcm = new Dynamicqcm();
        $dynamicqcm->setTheme('SCRUM_AGILITE');
        $dynamicqcm->setNbrQuestion(10);
        $manager->persist($dynamicqcm);
    }
    public function fixtureForUser(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $faker = Factory::create('fr_FR');

            $user = new User();

            $user->setEmail($faker->unique()->email());
            $user->setName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setRoles(['ROLE_USER']);

            $password = $this->hasher->hashPassword($user, 'azerty');
            $user->setPassword($password);

            $manager->persist($user);
        }
    }
}
