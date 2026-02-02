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
    #[Route('/teacher', name: 'teacher')]
    public function indexTeacher(): Response
    {
        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}