<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\DocumentRepository;
use App\Repository\QuizRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeacherController extends AbstractController
{
    private DocumentRepository $documentRepository;
    private VideoRepository $videoRepository;
    private CourseRepository $courseRepository;
    private QuizRepository $quizRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        VideoRepository $videoRepository,
        CourseRepository $courseRepository,
        QuizRepository $quizRepository
    )
    {
        $this->documentRepository = $documentRepository;
        $this->videoRepository = $videoRepository;
        $this->courseRepository = $courseRepository;
        $this->quizRepository = $quizRepository;
    }

    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'TeacherController',
            'courses' => $this->courseRepository->findAll(),
            'pdfs' => $this->documentRepository->findAll(),
            'videos' => $this->videoRepository->findAll(),
            'quizzes' => $this->quizRepository->findBy([], ['id' => 'DESC']),
        ]);
    }
}