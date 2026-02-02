<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
        if ($this->getUser()) {
            // Redirige immédiatement vers la page d'accueil
            return $this->redirectToRoute('index');
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
}
