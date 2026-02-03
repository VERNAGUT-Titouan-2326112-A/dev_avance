<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * UserRepository
 *
 * Référentiel pour gérer les opérations de base de données concernant l'entité User.
 * Cette classe gère les opérations CRUD (Create, Read, Update, Delete) et les requêtes personnalisées
 * pour l'entité User, y compris ses sous-classes (Teacher et Student grâce à l'héritage).
 *
 * Interfaces implémentées :
 * - ServiceEntityRepository : fournit les méthodes de base pour interroger la base de données
 * - PasswordUpgraderInterface : implémente la mise à jour automatique du hashage des mots de passe
 *
 * Fonctionnalités :
 * - Recherche d'utilisateurs par email ou autres critères
 * - Mise à jour automatique du hashage des mots de passe (rehashing)
 * - Gestion cohérente de tous les types d'utilisateurs (User, Teacher, Student)
 *
 * @extends ServiceEntityRepository<User>
 *
 * @package App\Repository
 * @author Équipe de Développement
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructeur de UserRepository
     *
     * @param ManagerRegistry $registry Le registre Doctrine pour accéder à l'entity manager
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Met à jour le mot de passe hashé d'un utilisateur de manière sécurisée.
     *
     * Cette méthode est appelée automatiquement par Symfony lors de la validation d'une authentification.
     * Elle permet de rehashér les mots de passe avec un nouvel algorithme si l'algorithme
     * actuel devient obsolète ou si on souhaite changer vers un nouvel algorithme plus sécurisé.
     *
     * Processus :
     * 1. Vérifie que l'utilisateur est une instance de User
     * 2. Met à jour le mot de passe avec le nouveau hash
     * 3. Persiste l'entité en base de données
     * 4. Flush les changements
     *
     * @param PasswordAuthenticatedUserInterface $user              L'utilisateur dont on doit mettre à jour le mot de passe
     * @param string                              $newHashedPassword Le nouveau mot de passe hashé à assigner
     *
     * @throws UnsupportedUserException Si l'utilisateur n'est pas une instance de User
     *
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Vérifie que l'utilisateur est bien une instance de User
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Les instances de "%s" ne sont pas supportées.', $user::class));
        }

        // Définit le nouveau mot de passe hashé
        $user->setPassword($newHashedPassword);

        // Persiste les changements en base de données
        $this->getEntityManager()->persist($user);

        // Flush pour valider les changements
        $this->getEntityManager()->flush();
    }

    // Les méthodes suivantes sont des exemples commentés pour montrer comment créer des requêtes personnalisées
    // Ils peuvent être décommentés et adaptés selon les besoins du projet

    // /**
    //  * Recherche les utilisateurs par un champ spécifique.
    //  *
    //  * @param mixed $value La valeur à rechercher
    //  *
    //  * @return User[] Les utilisateurs correspondant à la recherche
    //  */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('u.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // /**
    //  * Recherche un utilisateur par un champ spécifique.
    //  *
    //  * @param mixed $value La valeur à rechercher
    //  *
    //  * @return User|null L'utilisateur trouvé ou null
    //  */
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
