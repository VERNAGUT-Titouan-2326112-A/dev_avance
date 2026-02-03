<?php

namespace App\DataFixtures;

use App\Entity\Dynamicqcm;
use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * AppFixtures
 *
 * Classe de fixtures (données de test) pour charger les données initiales dans la base de données.
 * Les fixtures sont utiles pour le développement et les tests, et permettent de remplir rapidement
 * la base de données avec des données fictives réalistes.
 *
 * Données générées :
 * - 1 QCM dynamique (Questionnaire à Choix Multiples) avec le thème SCRUM_AGILITE
 * - 400 utilisateurs (200 boucles × 2 utilisateurs par boucle) : environ 200 étudiants et 200 professeurs
 *   avec des données générées aléatoirement par Faker
 *
 * Utilise :
 * - Faker : une bibliothèque pour générer des données fictives réalistes (noms, emails, etc.)
 * - UserPasswordHasherInterface : pour hashér les mots de passe de manière sécurisée
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class AppFixtures extends Fixture
{
    /**
     * Service Symfony pour hasher les mots de passe de manière sécurisée.
     * Utilise un algorithme de hachage configurable (bcrypt, Argon2, etc.)
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * Constructeur de AppFixtures
     *
     * @param UserPasswordHasherInterface $hasher Service pour hasher les mots de passe
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Charge toutes les fixtures dans la base de données.
     *
     * Cette méthode est appelée par Doctrine Fixtures Bundle et orchestre
     * le chargement de tous les ensembles de données.
     *
     * Processus :
     * 1. Charge les QCM dynamiques via fixtureForQcm()
     * 2. Charge les utilisateurs (Étudiants et Professeurs) via fixtureForUser()
     * 3. Flush les changements pour persister tout en base de données
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Charge les QCM
        $this->fixtureForQcm($manager);

        // Charge les utilisateurs (Étudiants et Professeurs)
        $this->fixtureForUser($manager);

        // Persiste tous les changements en base de données
        $manager->flush();
    }

    /**
     * Crée et charge un QCM dynamique de test dans la base de données.
     *
     * Cette méthode crée un seul QCM avec les caractéristiques suivantes :
     * - Thème : SCRUM_AGILITE (agile methodologie SCRUM)
     * - Nombre de questions : 10
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister l'entité
     *
     * @return void
     */
    public function fixtureForQcm(ObjectManager $manager): void
    {
        // Crée une nouvelle instance de QCM
        $dynamicqcm = new Dynamicqcm();

        // Définit le thème du QCM
        $dynamicqcm->setTheme('SCRUM_AGILITE');

        // Définit le nombre de questions que le QCM contient
        $dynamicqcm->setNbrQuestion(10);

        // Marque l'entité pour persistance
        $manager->persist($dynamicqcm);
    }

    /**
     * Crée et charge 400 utilisateurs fictifs (étudiants et professeurs) en base de données.
     *
     * Processus :
     * 1. Initialise Faker avec la locale française pour générer des données réalistes
     * 2. Exécute une boucle pour créer 400 utilisateurs
     * 3. Pour chaque utilisateur :
     *    - Génère aléatoirement un type d'utilisateur (Student ou Teacher)
     *    - Génère une adresse email unique avec Faker
     *    - Génère un prénom et un nom réaliste avec Faker
     *    - Crée un mot de passe par défaut "azerty" et le hache de manière sécurisée
     *    - Persiste l'utilisateur en base de données
     *
     * Données générées avec Faker :
     * - Email unique : Faker::email() + unique()
     * - Prénom : Faker::firstName()
     * - Nom de famille : Faker::lastName()
     * - Mot de passe : "azerty" (hardcodé pour faciliter les tests) après hachage
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function fixtureForUser(ObjectManager $manager): void
    {
        // Initialise la bibliothèque Faker avec la locale française pour des données réalistes
        $faker = Factory::create('fr_FR');

        // Boucle 200 fois pour créer 400 utilisateurs
        for ($i = 1; $i <= 20; $i++) {
            // Boucle interne : crée 2 utilisateurs par itération externe (20 × 20 = 400)
            for ($i = 0; $i < 20; $i++) {
                // Génère aléatoirement un type d'utilisateur (Student ou Teacher)
                // 50% de chance d'être un Student, 50% d'être un Teacher
                if ($faker->boolean()) {
                    $user = new Student();
                } else {
                    $user = new Teacher();

                    if ($faker->boolean(20)) {
                        $user->setRoles(['ROLE_ADMIN']);
                    }
                }

                // Génère une adresse email unique avec Faker
                $user->setEmail($faker->unique()->email());

                // Génère un prénom réaliste en français
                $user->setName($faker->firstName());

                // Génère un nom de famille réaliste en français
                $user->setLastName($faker->lastName());

                // Crée un mot de passe par défaut et le hache de manière sécurisée
                // IMPORTANT : En production, cela ne doit jamais être le mot de passe réel
                $password = $this->hasher->hashPassword($user, 'azerty');
                $user->setPassword($password);

                // Marque l'utilisateur pour persistance en base de données
                $manager->persist($user);
            }
        }
    }
}
