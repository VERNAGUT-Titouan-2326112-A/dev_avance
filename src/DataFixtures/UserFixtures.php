<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * UserFixtures
 *
 * Classe de fixtures pour charger les utilisateurs (Étudiants et Professeurs) en base de données.
 * Génère 400 utilisateurs fictifs avec des données réalistes via Faker.
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class UserFixtures extends Fixture
{
    /**
     * Service Symfony pour hasher les mots de passe de manière sécurisée.
     * Utilise un algorithme de hachage configurable (bcrypt, Argon2, etc.)
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * Constructeur de UserFixtures
     *
     * @param UserPasswordHasherInterface $hasher Service pour hasher les mots de passe
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Charge tous les utilisateurs en base de données.
     *
     * Crée 400 utilisateurs (environ 50% Étudiants, 50% Professeurs)
     * avec des données générées aléatoirement par Faker.
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Initialise Faker avec la locale française
        $faker = Factory::create('fr_FR');

        // Crée 400 utilisateurs
        for ($i = 0; $i < 20; $i++) {
            // Génère aléatoirement un type d'utilisateur (Student ou Teacher)
            if ($faker->boolean()) {
                $user = new Student();
            } else {
                $user = new Teacher();

                // 20% de chance qu'un professeur soit administrateur
                if ($faker->boolean(20)) {
                    $user->setRoles(['ROLE_ADMIN']);
                }
            }

            // Définit l'email unique
            $user->setEmail($faker->unique()->email());

            // Définit le prénom
            $user->setName($faker->firstName());

            // Définit le nom de famille
            $user->setLastName($faker->lastName());

            // Hache et définit le mot de passe (azerty pour les tests)
            $hashedPassword = $this->hasher->hashPassword($user, 'azerty');
            $user->setPassword($hashedPassword);

            // Marque l'utilisateur pour persistance
            $manager->persist($user);
        }

        // Persiste tous les utilisateurs en base de données
        $manager->flush();
    }
}
