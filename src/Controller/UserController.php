<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
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