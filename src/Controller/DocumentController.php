<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Video;
use App\Repository\CourseRepository;
use App\Repository\DocumentRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * DocumentController
 *
 * Contrôleur responsable de la gestion des ressources pédagogiques :
 * - Ajout de Documents PDF
 * - Ajout de Vidéos
 *
 * Chaque ressource est obligatoirement associée à un Cours.
 *
 * Ce contrôleur gère :
 * - La récupération des données du formulaire
 * - La validation des entrées (cours, fichier, type MIME)
 * - L’upload physique des fichiers sur le serveur
 * - La création et l’enregistrement des entités en base de données
 *
 * @package App\Controller
 */
class DocumentController extends AbstractController
{
    /**
     * Repository permettant la gestion des Documents en base.
     */
    private DocumentRepository $documentRepository;

    /**
     * Repository permettant la gestion des Vidéos en base.
     */
    private VideoRepository $videoRepository;

    /**
     * Repository permettant la récupération des Cours.
     */
    private CourseRepository $courseRepository;

    /**
     * Injection des dépendances via le constructeur.
     *
     * @param DocumentRepository $documentRepository
     * @param VideoRepository $videoRepository
     * @param CourseRepository $courseRepository
     */
    public function __construct(
        DocumentRepository $documentRepository,
        VideoRepository $videoRepository,
        CourseRepository $courseRepository
    ) {
        $this->documentRepository = $documentRepository;
        $this->videoRepository = $videoRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Ajout d’un Document PDF à un Cours.
     *
     * Route : /add_pdf
     *
     * Processus :
     * 1. Récupération du fichier, du titre et du cours
     * 2. Vérification qu’un cours est sélectionné
     * 3. Vérification de l’existence du cours
     * 4. Vérification du type MIME (PDF uniquement)
     * 5. Génération d’un nom unique pour éviter les conflits
     * 6. Déplacement du fichier sur le serveur
     * 7. Création de l’entité Document
     * 8. Enregistrement en base
     * 9. Message Flash de succès ou d’erreur
     */
    #[Route('/add_pdf', name: 'add_pdf')]
    public function addPdf(Request $request): Response
    {
        $file = $request->files->get('pdfFile');
        $title = $request->request->get('pdfTitle');
        $courseId = $request->request->get('courseId');

        // Vérifie qu’un cours est sélectionné
        if (!$courseId) {
            $this->addFlash('error', 'Aucun cours sélectionné.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Recherche du cours en base
        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            $this->addFlash('error', 'Cours introuvable.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Vérifie que le fichier est un PDF valide
        if ($file && $file->getMimeType() === 'application/pdf') {

            // Génère un nom unique pour éviter les collisions
            $filename = uniqid() . '-' . $file->getClientOriginalName();

            // Déplace le fichier dans le dossier configuré (pdf_directory)
            $file->move($this->getParameter('pdf_directory'), $filename);

            // Création de l’entité Document
            $pdf = new Document();
            $pdf->setTitle($title);
            $pdf->setPath($filename);
            $pdf->setCourse($course);

            // Enregistrement en base
            $this->documentRepository->save($pdf, true);

            $this->addFlash('success', 'Document ajouté avec succès !');
        } else {
            $this->addFlash('error', 'Fichier invalide.');
        }

        return $this->redirectToRoute('app_teacher_home');
    }

    /**
     * Ajout d’une Vidéo à un Cours.
     *
     * Route : /video/add
     * Méthode : POST
     *
     * Processus :
     * 1. Récupération du fichier, du titre et du cours
     * 2. Vérification qu’un cours est sélectionné
     * 3. Vérification de l’existence du cours
     * 4. Vérification qu’un fichier est envoyé
     * 5. Vérification que le fichier est bien une vidéo (video/*)
     * 6. Nettoyage du nom de fichier
     * 7. Génération d’un nom unique sécurisé
     * 8. Déplacement du fichier sur le serveur
     * 9. Création de l’entité Video
     * 10. Enregistrement en base
     */
    #[Route('/video/add', name: 'add_video', methods: ['POST'])]
    public function addVideo(Request $request): Response
    {
        $file = $request->files->get('videoFile');
        $title = $request->request->get('videoTitle');
        $courseId = $request->request->get('courseId');

        if (!$courseId) {
            $this->addFlash('error', 'Aucun cours sélectionné.');
            return $this->redirectToRoute('app_teacher_home');
        }

        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            $this->addFlash('error', 'Cours introuvable.');
            return $this->redirectToRoute('app_teacher_home');
        }

        if (!$file) {
            $this->addFlash('error', 'Aucun fichier vidéo envoyé.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Vérifie que le type MIME commence par "video/"
        if (!str_starts_with($file->getMimeType(), 'video/')) {
            $this->addFlash('error', 'Le fichier doit être une vidéo.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Nettoyage et sécurisation du nom de fichier
        $originalName = $file->getClientOriginalName();
        $info = pathinfo($originalName);
        $filenameOnly = $info['filename'];
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filenameOnly);
        $extension = $file->guessExtension();

        // Génère un nom unique sécurisé
        $filename = uniqid() . '-' . $safeName . '.' . $extension;

        // Déplace le fichier dans le dossier configuré (video_directory)
        $file->move($this->getParameter('video_directory'), $filename);

        // Création de l’entité Video
        $video = new Video();
        $video->setTitle($title);
        $video->setPath($filename);
        $video->setCourse($course);

        // Enregistrement en base
        $this->videoRepository->save($video, true);

        $this->addFlash('success', 'Vidéo ajoutée avec succès !');

        return $this->redirectToRoute('app_teacher_home');
    }
}
