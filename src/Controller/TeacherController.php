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

class TeacherController extends AbstractController
{
    private DocumentRepository $documentRepository;
    private VideoRepository $videoRepository;
    private CourseRepository $courseRepository;
    private QuizRepository $quizRepository;
    private QuizAttemptRepository $quizAttemptRepository;

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

    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        // On récupère les données proprement
        $courses = $this->courseRepository->findAll();
        $pdfs = $this->documentRepository->findAll();
        $videos = $this->videoRepository->findAll();
        $quizzes = $this->quizRepository->findAll();
        $results = $this->quizAttemptRepository->findAll();

        return $this->render('User/Professor/index.html.twig', [
            'courses' => $courses,
            'pdfs' => $pdfs,
            'videos' => $videos,
            'quizzes' => $quizzes,
            'results' => $results,
        ]);
    }
}