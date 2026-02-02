<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teacher
 *
 * Entité représentant un professeur du système.
 * Hérite de User et ajoute le rôle ROLE_TEACHER automatiquement.
 *
 * Utilise l'héritage de table simple (Single Table Inheritance) de Doctrine.
 * Cela signifie qu'un professeur est un utilisateur avec un type discriminant 'teacher'.
 *
 * Fonctionnalités :
 * - Hérite de tous les attributs et méthodes de User (id, email, password, etc.)
 * - Ajoute automatiquement le rôle ROLE_TEACHER aux rôles de l'utilisateur
 * - La classe elle-même ne définit pas de propriétés supplémentaires
 *
 * @package App\Entity
 * @author Équipe de Développement
 */
#[ORM\Entity]
class Teacher extends User
{
    /**
     * Récupère tous les rôles du professeur.
     *
     * Processus :
     * 1. Récupère tous les rôles de la classe parente User (qui inclut ROLE_USER)
     * 2. Ajoute le rôle ROLE_TEACHER spécifique aux professeurs
     * 3. Supprime les doublons avec array_unique()
     *
     * @return array Liste des rôles du professeur incluant ROLE_USER et ROLE_TEACHER
     *
     * @see User::getRoles()
     */
    public function getRoles(): array
    {
        // Récupère les rôles hérités de la classe parente (User)
        $roles = parent::getRoles();
        // Ajoute le rôle spécifique au professeur
        $roles[] = 'ROLE_TEACHER';

        // Retourne les rôles dédupliqués
        return array_unique($roles);
    }
}