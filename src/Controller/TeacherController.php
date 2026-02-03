<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeacherController extends AbstractController
{
    #[Route('/teacher', name: 'app_teacher_home')]
    public function index(): Response
    {
        // On pointe vers ton template existant
        return $this->render('User/Professor/index.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }
}