<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * UserController
 *
 * Contrôleur principal pour les pages d'accueil et de gestion des utilisateurs.
 * Ce contrôleur gère l'affichage de la page d'index qui est la page d'accueil de l'application.
 *
 * @package App\Controller
 * @author Équipe de Développement
 */
class UserController extends AbstractController
{
    private DocumentRepository $documentRepository;
    private CourseRepository $courseRepository;

    public function __construct(DocumentRepository $documentRepository, CourseRepository $courseRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->courseRepository = $courseRepository;
    }
    /**
     * Affiche la page d'accueil de l'application.
     *
     * Cette route correspond à l'URL racine '/' et affiche la page d'index.
     * Elle est accessible à tous les utilisateurs, qu'ils soient authentifiés ou non.
     * La page affichée peut contenir des informations publiques ou spécifiques
     * à l'utilisateur connecté selon le template Twig.
     *
     * @return Response La page d'accueil (template User/index.html.twig)
     */
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Affiche le template de la page d'accueil
        return $this->render('User/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/teacher', name: 'teacher')]
    public function indexTeacher(): Response
    {
        $pdfs = $this->documentRepository->findAll();

        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'UserController',
            'pdfs' => $pdfs,
        ]);
    }
}