<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class CourseController extends AbstractController
{
    private CourseRepository $courseRepository;
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    #[Route('/', name: 'dashboard')]
    public function dashBoard(): Response
    {
        $courses = $this->courseRepository->findAll();

        return $this->render('User/dashBoard.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/create', name: 'create_dashboard')]
    public function createDashBoard(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $subject = $request->request->get('subject');
            $level = $request->request->get('level');

            $course = new Course();
            $course->setTitle($title);
            $course->setSubject($subject);
            $course->setLevel($level);

            $this->courseRepository->save($course, true);

            $this->addFlash('success', 'Le cours a été créé avec succès !');

        }

        $courses = $this->courseRepository->findAll();

        return $this->render('User/dashBoard.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }


        if ($request->isXmlHttpRequest()) {
            return $this->render('course/_form.html.twig', [
                'course' => $course,
                'form' => $form,
            ]);
        }

        // Sinon, on rend la page complète
        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }
        #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        $courses = $this->courseRepository->findAll();
        return $this->redirectToRoute('dashboard', [
            'courses' => $courses,
        ], Response::HTTP_SEE_OTHER);
    }
}
