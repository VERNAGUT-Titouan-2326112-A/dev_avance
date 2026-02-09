<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\DocumentRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeacherController extends AbstractController
{
    private DocumentRepository $documentRepository;
    private VideoRepository $videoRepository;
    private CourseRepository $courseRepository;

    public function __construct(DocumentRepository $documentRepository, VideoRepository $videoRepository, CourseRepository $courseRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->videoRepository = $videoRepository;
        $this->courseRepository = $courseRepository;
    }
    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        $pdfs = $this->documentRepository->findAll();
        $videos = $this->videoRepository->findAll();
        $courses = $this->courseRepository->findAll();
        // On pointe vers ton template existant
        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'TeacherController',
            'courses' => $courses,
            'pdfs' => $pdfs,
            'videos' => $videos,
        ]);
    }
}