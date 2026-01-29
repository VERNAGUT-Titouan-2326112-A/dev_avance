<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('User/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/student', name: 'index_student')]
    public function indexStudent(): Response
    {
        return $this->render('User/Student/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}