<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * AppFixtures
 *
 * Classe orchestratrice qui charge toutes les fixtures dans le bon ordre.
 * Utilise OrderedFixtureInterface pour garantir que les fixtures dépendantes
 * sont chargées dans le bon ordre.
 *
 * Ordre de chargement :
 * 1. DynamicqcmFixtures (QCM indépendants)
 * 2. UserFixtures (Utilisateurs indépendants)
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * Retourne l'ordre de chargement de cette fixture.
     * Les fixtures avec un ordre inférieur sont chargées en premier.
     *
     * @return int L'ordre de chargement (0 = premier)
     */
    public function getOrder(): int
    {
        return 10; // Chargée en dernier (après les autres)
    }

    /**
     * Load est vide ici car les fixtures spécifiques
     * (DynamicqcmFixtures et UserFixtures) gèrent leur propre chargement.
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Rien à faire ici, les autres fixtures gèrent leur chargement
    }
}
