<?php

namespace App\Security;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * AppAuthenticator
 *
 * Authenticateur personnalisé pour gérer la connexion des utilisateurs (Étudiants et Professeurs).
 * Ce service implémente un formulaire de connexion avec authentification basée sur l'email et le mot de passe.
 *
 * Fonctionnalités principales :
 * - Validation de l'email et du mot de passe
 * - Vérification du type d'utilisateur (Étudiant ou Professeur)
 * - Contrôle d'accès restrictif selon le type de compte
 * - Protection CSRF et gestion de "Remember Me"
 * - Redirection vers la page cible après authentification réussie
 *
 * @package App\Security
 * @author Équipe de Développement
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * Route nommée de la page de connexion utilisée pour rediriger lors de l'authentification.
     * Cette constante est utilisée dans la méthode getLoginUrl() pour générer l'URL de redirection.
     */
    public const LOGIN_ROUTE = 'app_login';

    /**
     * Constructeur de AppAuthenticator
     *
     * @param UrlGeneratorInterface $urlGenerator        Service Symfony pour générer les URLs des routes nommées
     * @param UserRepository        $userRepository      Référentiel pour accéder aux utilisateurs en base de données
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository
    ) {
    }

    /**
     * Effectue l'authentification de l'utilisateur en validant ses identifiants et son type de compte.
     *
     * Processus :
     * 1. Récupère l'email et le type de connexion (étudiant ou professeur) du formulaire
     * 2. Stocke l'email en session pour affichage ultérieur du nom d'utilisateur
     * 3. Crée un Passport contenant les badges de sécurité et les identifiants :
     *    - UserBadge : charge l'utilisateur et valide que son type correspond au formulaire utilisé
     *    - PasswordCredentials : contient le mot de passe à valider (la validation est effectuée par Symfony)
     *    - CsrfTokenBadge : protège contre les attaques CSRF
     *    - RememberMeBadge : permet la persistance de la session (fonctionnalité "Se souvenir de moi")
     *
     * @param Request $request L'objet Request contenant les données du formulaire de connexion
     *
     * @return Passport Un objet Passport représentant l'authentification à valider
     *
     * @throws UserNotFoundException              Si l'utilisateur n'existe pas en base de données
     * @throws CustomUserMessageAuthenticationException Si l'utilisateur n'a pas le bon rôle (ex: un étudiant tentant d'accéder au formulaire professeur)
     */
    public function authenticate(Request $request): Passport
    {
        // Extraction de l'email saisi dans le formulaire
        $email = $request->getPayload()->getString('email');

        // Extraction du type de connexion : 'teacher' ou 'student'
        $loginType = $request->getPayload()->getString('_login_type');

        // Sauvegarde de l'email en session pour les besoins de l'application (affichage de la dernière tentative)
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Création du Passport (conteneur de sécurité pour Symfony)
        return new Passport(
            // UserBadge : charge l'utilisateur depuis la base de données et applique une fonction de callback personnalisée
            new UserBadge($email, function ($userIdentifier) use ($loginType) {
                // Recherche l'utilisateur par son email
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                // Si l'utilisateur n'existe pas, lève une exception
                if (!$user) {
                    throw new UserNotFoundException();
                }

                // Vérifie que le professeur ne tente pas d'accéder avec le formulaire d'étudiant
                if ($loginType === 'teacher' && !$user instanceof Teacher) {
                    throw new CustomUserMessageAuthenticationException('Accès réservé aux professeurs. Utilisez le formulaire Étudiant.');
                }

                // Vérifie qu'un étudiant ne tente pas d'accéder avec le formulaire de professeur
                if ($loginType === 'student' && !$user instanceof Student) {
                    throw new CustomUserMessageAuthenticationException('Accès réservé aux étudiants. Utilisez le formulaire Professeur.');
                }

                // Retourne l'utilisateur validé
                return $user;
            }),
            // PasswordCredentials : contient le mot de passe à valider (validation automtique par Symfony)
            new PasswordCredentials($request->getPayload()->getString('password')),
            // Badges de sécurité supplémentaires
            [
                // CsrfTokenBadge : valide le token CSRF pour prévenir les attaques par falsification de demande intersite
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                // RememberMeBadge : gère la fonctionnalité "Se souvenir de moi" pour la persistance de session
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Effectue la redirection après une authentification réussie.
     *
     * Logique de redirection (dans l'ordre de priorité) :
     * 1. Utilise le chemin cible stocké en session si l'utilisateur a tenté d'accéder à une page protégée
     *    avant la redirection vers la connexion
     * 2. Utilise le chemin cible passé en paramètre POST (_target_path) si fourni
     * 3. Redirige vers la page d'accueil (index) par défaut
     *
     * @param Request       $request       L'objet Request représentant la requête HTTP
     * @param TokenInterface $token        Le token d'authentification de l'utilisateur (contient l'utilisateur authentifié)
     * @param string        $firewallName  Le nom du firewall utilisé (défini dans la configuration de sécurité)
     *
     * @return Response|null Une réponse de redirection HTTP ou null (ce qui laisse Symfony traiter la suite)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifie s'il y a un chemin cible stocké en session (l'utilisateur tentait d'accéder à une page avant la connexion)
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Vérifie s'il y a un chemin cible fourni en paramètre POST du formulaire
        $targetPath = $request->getPayload()->getString('_target_path');
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        // Redirection par défaut vers la page d'accueil
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    /**
     * Génère et retourne l'URL de la page de connexion.
     *
     * Cette méthode est appelée par Symfony lorsqu'une authentification échoue ou qu'un accès
     * non authentifié est détecté. Elle redirige l'utilisateur vers la page de connexion.
     *
     * @param Request $request L'objet Request représentant la requête HTTP actuelle
     *
     * @return string L'URL de la page de connexion générée à partir de la route nommée 'app_login'
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}