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

/**
 * CourseController
 *
 * Contrôleur responsable de la gestion des Cours dans le Dashboard.
 *
 * Fonctionnalités :
 * - Affichage de tous les cours
 * - Création d’un nouveau cours
 * - Modification d’un cours existant
 * - Suppression d’un cours
 *
 * Ce contrôleur constitue le point central de gestion
 * des entités Course côté professeur.
 *
 * @package App\Controller
 */
#[Route('/dashboard')]
final class CourseController extends AbstractController
{
    /**
     * Repository permettant la gestion des Cours en base.
     */
    private CourseRepository $courseRepository;

    /**
     * Injection du CourseRepository.
     *
     * @param CourseRepository $courseRepository
     */
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Affichage du Dashboard.
     *
     * Route : /dashboard/
     *
     * Récupère tous les cours en base
     * et les transmet à la vue.
     */
    #[Route('/', name: 'dashboard')]
    public function dashBoard(): Response
    {
        $courses = $this->courseRepository->findAll();

        return $this->render('User/dashBoard.html.twig', [
            'courses' => $courses,
        ]);
    }

    /**
     * Création d’un nouveau Cours.
     *
     * Route : /dashboard/create
     *
     * Processus :
     * 1. Vérifie si la requête est en POST
     * 2. Récupère les données du formulaire
     * 3. Crée une nouvelle entité Course
     * 4. Enregistre en base
     * 5. Affiche un message de succès
     */
    #[Route('/create', name: 'create_dashboard')]
    public function createDashBoard(Request $request): Response
    {
        if ($request->isMethod('POST')) {

            // Récupération des données du formulaire
            $title = $request->request->get('title');
            $subject = $request->request->get('subject');
            $level = $request->request->get('level');

            // Création de l'entité Course
            $course = new Course();
            $course->setTitle($title);
            $course->setSubject($subject);
            $course->setLevel($level);

            // Sauvegarde en base
            $this->courseRepository->save($course, true);

            $this->addFlash('success', 'Le cours a été créé avec succès !');
        }

        // Rafraîchit la liste des cours
        $courses = $this->courseRepository->findAll();

        return $this->render('User/dashBoard.html.twig', [
            'courses' => $courses,
        ]);
    }

    /**
     * Modification d’un cours existant.
     *
     * Route : /dashboard/{id}/edit
     * Méthodes : GET, POST
     *
     * Utilise le Form Component Symfony (CourseType).
     *
     * Processus :
     * 1. Création du formulaire lié à l'entité
     * 2. Traitement de la requête
     * 3. Si valide → flush en base
     * 4. Redirection vers le dashboard
     *
     * Gestion spéciale :
     * - Si requête AJAX → retourne uniquement le formulaire partiel
     * - Sinon → affiche la page complète
     */
    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        // Si formulaire soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        // Si requête AJAX (modale par exemple)
        if ($request->isXmlHttpRequest()) {
            return $this->render('course/_form.html.twig', [
                'course' => $course,
                'form' => $form,
            ]);
        }

        // Sinon, affichage page complète
        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d’un cours.
     *
     * Route : /dashboard/{id}
     * Méthode : POST
     *
     * Processus :
     * 1. Vérification du token CSRF
     * 2. Suppression de l’entité
     * 3. Flush en base
     * 4. Redirection vers le dashboard
     */
    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Sécurisation via token CSRF
        if ($this->isCsrfTokenValid(
            'delete'.$course->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'dashboard',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
