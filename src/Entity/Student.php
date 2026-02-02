<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Student
 *
 * Entité représentant un étudiant du système.
 * Hérite de User et ajoute le rôle ROLE_STUDENT automatiquement.
 *
 * Utilise l'héritage de table simple (Single Table Inheritance) de Doctrine.
 * Cela signifie qu'un étudiant est un utilisateur avec un type discriminant 'student'.
 *
 * Fonctionnalités :
 * - Hérite de tous les attributs et méthodes de User (id, email, password, etc.)
 * - Ajoute automatiquement le rôle ROLE_STUDENT aux rôles de l'utilisateur
 * - La classe elle-même ne définit pas de propriétés supplémentaires pour le moment
 * - Peut être étendue à l'avenir avec des propriétés spécifiques (numéro d'étudiant, classe, etc.)
 *
 * @package App\Entity
 * @author Équipe de Développement
 */
#[ORM\Entity]
class Student extends User
{
    /**
     * Récupère tous les rôles de l'étudiant.
     *
     * Processus :
     * 1. Récupère tous les rôles de la classe parente User (qui inclut ROLE_USER)
     * 2. Ajoute le rôle ROLE_STUDENT spécifique aux étudiants
     * 3. Supprime les doublons avec array_unique()
     *
     * @return array Liste des rôles de l'étudiant incluant ROLE_USER et ROLE_STUDENT
     *
     * @see User::getRoles()
     */
    public function getRoles(): array
    {
        // Récupère les rôles hérités de la classe parente (User)
        $roles = parent::getRoles();
        // Ajoute le rôle spécifique à l'étudiant
        $roles[] = 'ROLE_STUDENT';

        // Retourne les rôles dédupliqués
        return array_unique($roles);
    }
}