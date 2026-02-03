<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController
 *
 * Contrôleur responsable de la gestion des opérations d'authentification et de déconnexion.
 * Ce contrôleur utilise le système de sécurité intégré de Symfony pour gérer les formulaires de connexion
 * et les tentatives de déconnexion.
 *
 * Routes gérées :
 * - /login : affiche la page de connexion et traite les erreurs d'authentification
 * - /logout : gère la déconnexion de l'utilisateur
 *
 * @package App\Controller
 * @author Équipe de Développement
 */
class SecurityController extends AbstractController
{
    /**
     * Référentiel pour accéder aux données utilisateur en base de données.
     * Utilisé pour persister les nouveaux utilisateurs lors de l'inscription
     * et pour rechercher les utilisateurs lors de l'authentification.
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Constructeur du SecurityController
     *
     * @param UserRepository $userRepository Référentiel pour les opérations sur les utilisateurs
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Affiche la page de connexion et gère l'authentification.
     *
     * Processus :
     * 1. Vérifie si un utilisateur est déjà authentifié
     * 2. Si oui, le redirige immédiatement vers la page d'accueil
     * 3. Si non, récupère les erreurs d'authentification et le dernier identifiant saisi
     * 4. Affiche la page Twig avec les données necessaires au formulaire
     *
     * @param AuthenticationUtils $authenticationUtils Service Symfony pour gérer les erreurs et les données d'authentification
     *
     * @return Response La page de connexion (template Twig)
     *
     * @see AppAuthenticator pour la logique d'authentification personnalisée
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifie si un utilisateur est déjà connecté
        if ($user = $this->getUser()){

            if ($user instanceof Student) {
                return $this->redirectToRoute('app_student_home');
            }

            if ($user instanceof Teacher) {
                return $this->redirectToRoute('app_teacher_home');
            }
        }

        // Récupère la dernière erreur d'authentification s'il en existe une
        // Cette erreur sera affichée à l'utilisateur pour l'informer de son problème
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier identifiant (email) saisi par l'utilisateur
        // Cela permet de préremplir le champ email dans le formulaire pour meilleure UX
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affiche le template de connexion avec les données et erreurs
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     *
     * Cette méthode ne contient pas de logique réelle. Le corps du logout est entièrement
     * géré par le firewall de sécurité Symfony configuré dans config/packages/security.yaml.
     * Le firewall intercepte la requête vers cette route et effectue la déconnexion avant
     * que le contrôleur soit réellement exécuté.
     *
     * @throws \LogicException Cette exception est levée pour signaler qu'elle ne doit pas être appelée directement
     *
     * @see config/packages/security.yaml pour la configuration du firewall et du logout
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode ne doit pas être appelée directement. Elle est interceptée par le firewall de sécurité.');
    }
    /**
     * Gère l'inscription d'un nouvel utilisateur (Étudiant ou Professeur).
     *
     * Cette méthode affiche un formulaire d'inscription et traite la soumission de ce formulaire.
     * Elle gère le polymorphisme des utilisateurs en créant soit un Student soit un Teacher
     * selon le choix de l'utilisateur.
     *
     * Processus :
     * 1. Affichage du formulaire (GET) :
     *    - Affiche le formulaire vierge pour que l'utilisateur puisse s'inscrire
     *
     * 2. Soumission du formulaire (POST) :
     *    a) Récupère les données du formulaire
     *    b) Vérifie que le formulaire est valide (email unique, mots de passe correspondants, etc.)
     *    c) Crée une instance du bon type d'utilisateur (Teacher ou Student) selon le choix
     *    d) Copie les données du formulaire temporaire vers l'utilisateur réel
     *    e) Hache le mot de passe de manière sécurisée avec bcrypt/Argon2
     *    f) Persiste l'utilisateur en base de données
     *    g) Affiche un message de succès via Flash
     *    h) Redirige vers la page de connexion
     *
     * Raison du pattern User temporaire :
     *    Le formulaire crée d'abord une instance User temporaire pour validation.
     *    Ensuite, on crée l'utilisateur réel (Teacher ou Student) car ces classes
     *    héritent de User et overridendent la méthode getRoles(). Cela garantit que
     *    chaque utilisateur a le bon rôle assigné automatiquement.
     *
     * @param Request                        $request             L'objet Request contenant les données POST du formulaire
     * @param UserPasswordHasherInterface    $passwordHasher      Service Symfony pour hasher les mots de passe de manière sécurisée
     * @param EntityManagerInterface         $entityManager       Gestionnaire d'entités Doctrine (actuellement non utilisé, mais injecté)
     *
     * @return Response Une réponse HTTP contenant soit :
     *                  - Le formulaire d'inscription (si GET ou validation échouée)
     *                  - Une redirection vers la page de connexion (si succès)
     *
     * @see RegistrationFormType pour la structure et validation du formulaire
     * @see Teacher pour le rôle assigné aux professeurs
     * @see Student pour le rôle assigné aux étudiants
     * @see UserPasswordHasherInterface pour la sécurisation des mots de passe
     */
    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Crée une instance User temporaire utilisée uniquement pour la validation du formulaire
        $user = new User();

        // Crée le formulaire d'inscription basé sur la classe RegistrationFormType
        // Ce formulaire contient les champs : email, plainPassword, userType, etc.
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Traite la requête : lie les données POST du formulaire à l'entité User
        // Cela peuple les propriétés de $user avec les données soumises
        $form->handleRequest($request);

        // Vérifie que le formulaire a été soumis ET que les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère le choix de type d'utilisateur depuis le formulaire ('teacher' ou 'student')
            $type = $form->get('userType')->getData();

            // Crée l'utilisateur réel du type spécifié
            // Cela garantit que le bon rôle sera assigné via la méthode getRoles() surchargée
            if ($type === 'teacher') {
                // Crée un nouvel utilisateur de type Professeur
                $realUser = new Teacher();
            } else {
                // Crée un nouvel utilisateur de type Étudiant (valeur par défaut)
                $realUser = new Student();
            }

            // Copie les données de l'utilisateur temporaire vers l'utilisateur réel
            $realUser->setEmail($user->getEmail());
            $realUser->setName($user->getName());
            $realUser->setLastName($user->getLastName());

            // Hache le mot de passe en clair et l'assigne de manière sécurisée
            // $passwordHasher utilise un algorithme sécurisé (bcrypt par défaut avec Symfony)
            // Le mot de passe en clair n'est JAMAIS stocké en base de données
            $realUser->setPassword($passwordHasher->hashPassword(
                $realUser,
                $form->get('plainPassword')->getData()
            ));

            // Persiste l'utilisateur en base de données
            // Le paramètre 'true' indique de flush immédiatement après
            // (si false, il faudrait appeler flush() manuellement plus tard)
            $this->userRepository->save($realUser, true);

            // Ajoute un message Flash de succès qui sera affiché au prochain rechargement de page
            // Le message est visible en haut de la page de connexion pour confirmer l'inscription
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

            // Redirige l'utilisateur vers la page de connexion après inscription réussie
            return $this->redirectToRoute('app_login');
        }

        // Affiche le formulaire d'inscription (en cas de GET ou si validation échouée)
        // Transmet le formulaire au template Twig pour rendu HTML
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
