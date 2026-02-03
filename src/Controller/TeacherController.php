<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeacherController extends AbstractController
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }
    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        $pdfs = $this->documentRepository->findAll();
        // On pointe vers ton template existant
        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'TeacherController',
            'pdfs' => $pdfs,
        ]);
    }
}