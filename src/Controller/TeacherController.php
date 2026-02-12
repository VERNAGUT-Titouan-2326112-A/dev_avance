<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\DocumentRepository;
use App\Repository\QuizRepository;
use App\Repository\VideoRepository;
use App\Repository\QuizAttemptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * TeacherController
 *
 * Contrôleur responsable de l’espace professeur.
 *
 * Ce contrôleur gère l’affichage du tableau de bord enseignant,
 * permettant au professeur d’accéder aux différentes ressources
 * pédagogiques :
 *
 * - Cours
 * - Documents (PDF, supports)
 * - Vidéos
 * - Quiz
 * - Résultats des étudiants (QuizAttempt)
 *
 * Il récupère les données via les Repository Doctrine
 * puis les transmet au template Twig correspondant.
 *
 * @package App\Controller
 */
class TeacherController extends AbstractController
{
    /**
     * Référentiel des documents pédagogiques
     *
     * @var DocumentRepository
     */
    private DocumentRepository $documentRepository;

    /**
     * Référentiel des vidéos pédagogiques
     *
     * @var VideoRepository
     */
    private VideoRepository $videoRepository;

    /**
     * Référentiel des cours
     *
     * @var CourseRepository
     */
    private CourseRepository $courseRepository;

    /**
     * Référentiel des quiz
     *
     * @var QuizRepository
     */
    private QuizRepository $quizRepository;

    /**
     * Référentiel des tentatives de quiz (résultats étudiants)
     *
     * @var QuizAttemptRepository
     */
    private QuizAttemptRepository $quizAttemptRepository;

    /**
     * Constructeur du TeacherController
     *
     * Injection de dépendances via le mécanisme d’Autowiring de Symfony.
     * Symfony injecte automatiquement les Repository nécessaires.
     *
     * @param DocumentRepository     $documentRepository
     * @param VideoRepository        $videoRepository
     * @param CourseRepository       $courseRepository
     * @param QuizRepository         $quizRepository
     * @param QuizAttemptRepository  $quizAttemptRepository
     */
    public function __construct(
        DocumentRepository $documentRepository,
        VideoRepository $videoRepository,
        CourseRepository $courseRepository,
        QuizRepository $quizRepository,
        QuizAttemptRepository $quizAttemptRepository
    )
    {
        $this->documentRepository = $documentRepository;
        $this->videoRepository = $videoRepository;
        $this->courseRepository = $courseRepository;
        $this->quizRepository = $quizRepository;
        $this->quizAttemptRepository = $quizAttemptRepository;
    }

    /**
     * Page d'accueil de l’espace professeur
     *
     * Route : /teacher
     *
     * Cette méthode :
     * 1. Récupère toutes les données nécessaires depuis la base de données
     *    via les différents Repository
     * 2. Transmet ces données au template Twig
     * 3. Retourne une réponse HTTP contenant la vue générée
     *
     * Données récupérées :
     * - Liste des cours
     * - Liste des documents PDF
     * - Liste des vidéos
     * - Liste des quiz
     * - Liste des résultats des étudiants
     *
     * @return Response Vue du tableau de bord professeur
     */
    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        // Récupération de tous les cours disponibles
        $courses = $this->courseRepository->findAll();

        // Récupération de tous les documents pédagogiques
        $pdfs = $this->documentRepository->findAll();

        // Récupération de toutes les vidéos
        $videos = $this->videoRepository->findAll();

        // Récupération de tous les quiz
        $quizzes = $this->quizRepository->findAll();

        // Récupération des résultats des étudiants (tentatives de quiz)
        $results = $this->quizAttemptRepository->findAll();

        // Rendu du template Twig avec les données
        return $this->render('User/Professor/index.html.twig', [
            'courses' => $courses,
            'pdfs' => $pdfs,
            'videos' => $videos,
            'quizzes' => $quizzes,
            'results' => $results,
        ]);
    }
}
