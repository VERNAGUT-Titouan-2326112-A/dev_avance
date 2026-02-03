<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * Entité de base représentant un utilisateur du système d'authentification.
 * Cette classe utilise l'héritage de table simple (Single Table Inheritance) de Doctrine,
 * ce qui signifie que tous les utilisateurs (Teacher, Student, User) sont stockés dans
 * la même table avec une colonne discriminante pour identifier le type.
 *
 * Interfaces implémentées :
 * - UserInterface : interface requise par Symfony Security pour les utilisateurs authentifiables
 * - PasswordAuthenticatedUserInterface : interface pour les utilisateurs utilisant une authentification par mot de passe
 *
 * Attributs :
 * - id : identifiant unique généré automatiquement
 * - email : adresse email unique de l'utilisateur (utilisée comme identifiant de connexion)
 * - roles : tableau des rôles assignés à l'utilisateur (ROLE_USER, ROLE_TEACHER, ROLE_STUDENT)
 * - password : mot de passe hashé de l'utilisateur
 * - name : prénom de l'utilisateur
 * - lastName : nom de famille de l'utilisateur
 *
 * Héritage :
 * Cette classe est la classe parente pour Student et Teacher, qui ajoutent des rôles spécifiques.
 *
 * @package App\Entity
 * @author Équipe de Développement
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['user' => User::class, 'student' => Student::class, 'teacher' => Teacher::class])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur.
     * Généré automatiquement par la base de données.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email unique de l'utilisateur.
     * Utilisée comme identifiant pour la connexion et comme clé unique en base de données.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Tableau des rôles assignés à l'utilisateur.
     * Les rôles couramment utilisés sont :
     * - ROLE_USER : rôle par défaut assigné à tous les utilisateurs
     * - ROLE_STUDENT : rôle spécifique aux étudiants
     * - ROLE_TEACHER : rôle spécifique aux professeurs
     *
     * @var list<string> Les rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé de l'utilisateur.
     * Jamais stocké en clair, toujours hashé avec un algorithme sécurisé (bcrypt, Argon2, etc.)
     * via Symfony\Component\PasswordHasher\PasswordHasherInterface
     *
     * @var string Le mot de passe hashé
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Prénom de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Nom de famille de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * Récupère l'identifiant unique de l'utilisateur.
     *
     * @return int|null L'ID de l'utilisateur, ou null si l'utilisateur n'a pas encore été persté en base de données
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'adresse email de l'utilisateur.
     *
     * @return string|null L'adresse email ou null si non définie
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse email de l'utilisateur.
     * L'email doit être unique en base de données.
     *
     * @param string $email L'adresse email à assigner
     *
     * @return self L'instance courante (pour le chaînage de méthodes)
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Récupère l'identifiant unique visible de l'utilisateur (utilisé par Symfony Security).
     * Dans notre cas, l'identifiant est l'email qui sert de clé de connexion.
     *
     * @see UserInterface
     *
     * @return string L'email de l'utilisateur utilisé comme identifiant
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Récupère tous les rôles de l'utilisateur incluant le rôle par défaut ROLE_USER.
     * Les rôles retournés sont uniques (pas de doublons).
     *
     * Processus :
     * 1. Récupère les rôles stockés de l'utilisateur
     * 2. Ajoute automatiquement ROLE_USER (rôle par défaut obligatoire)
     * 3. Supprime les doublons avec array_unique()
     *
     * @see UserInterface
     *
     * @return array Liste des rôles de l'utilisateur
     */
    public function getRoles(): array
    {
        // Récupère les rôles assignés à l'utilisateur
        $roles = $this->roles;
        // Garantit que chaque utilisateur a au minimum le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        // Retourne les rôles dédupliqués
        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     * Les rôles doivent être passés sous forme de tableau.
     *
     * @param list<string> $roles Les nouveaux rôles à assigner à l'utilisateur
     *
     * @return self L'instance courante (pour le chaînage de méthodes)
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Récupère le mot de passe hashé de l'utilisateur.
     * Le mot de passe est toujours stocké sous forme hashée, jamais en clair.
     *
     * @see PasswordAuthenticatedUserInterface
     *
     * @return string|null Le mot de passe hashé ou null si non défini
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe hashé de l'utilisateur.
     * IMPORTANT : Cette méthode doit recevoir un mot de passe déjà hashé,
     * jamais un mot de passe en clair. Utilisez UserPasswordHasherInterface pour hashér avant d'appeler cette méthode.
     *
     * @param string $password Le mot de passe hashé à assigner
     *
     * @return self L'instance courante (pour le chaînage de méthodes)
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les données sensibles de l'utilisateur (par exemple le mot de passe en clair temporaire).
     * Cette méthode est dépréciée et sera supprimée lors de la migration vers Symfony 8.
     * Elle est conservée pour compatibilité avec les anciennes versions.
     *
     * @deprecated À supprimer lors de la migration vers Symfony 8
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, à supprimer lors de la migration vers Symfony 8
    }

    /**
     * Récupère le prénom de l'utilisateur.
     *
     * @return string|null Le prénom ou null si non défini
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le prénom de l'utilisateur.
     *
     * @param string $name Le prénom à assigner
     *
     * @return self L'instance courante (pour le chaînage de méthodes)
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Récupère le nom de famille de l'utilisateur.
     *
     * @return string|null Le nom de famille ou null si non défini
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Définit le nom de famille de l'utilisateur.
     *
     * @param string $lastName Le nom de famille à assigner
     *
     * @return self L'instance courante (pour le chaînage de méthodes)
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }
}
