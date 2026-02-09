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

class DocumentController extends AbstractController
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

    #[Route('/add_pdf', name: 'add_pdf')]
    public function addPdf(Request $request): Response
    {

        $file = $request->files->get('pdfFile');
        $title = $request->request->get('pdfTitle');
        $courseId = $request->request->get('courseId');

        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            $this->addFlash('error', 'Cours introuvable.');
            return $this->redirectToRoute('app_teacher_home');
        }

        if ($file && $file->getMimeType() === 'application/pdf') {
            $filename = uniqid() . '-' . $file->getClientOriginalName();
            $file->move($this->getParameter('pdf_directory'), $filename);

            $pdf = new Document();
            $pdf->setTitle($title);
            $pdf->setPath($filename);
            $pdf->setCourse($course);

            $this->documentRepository->save($pdf, true);

            $this->addFlash('success', 'Document ajouté avec succès !');
        } else {
            $this->addFlash('error', 'Fichier invalide.');
        }
        $pdfs = $this->documentRepository->findAll();
        $videos = $this->videoRepository->findAll();
        return $this->redirectToRoute('app_teacher_home');
    }

    #[Route('/video/add', name: 'add_video', methods: ['POST'])]
    public function addVideo(Request $request): Response
    {
        // Récupération du fichier et du titre
        $file = $request->files->get('videoFile');
        $title = $request->request->get('videoTitle');

        $courseId = $request->request->get('courseId');

        $course = $this->courseRepository->find($courseId);

        if (!$course) {
            $this->addFlash('error', 'Cours introuvable.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Vérification du fichier
        if (!$file || !str_starts_with($file->getMimeType(), 'video/')) {
            $this->addFlash('error', 'Fichier vidéo invalide.');
            return $this->redirectToRoute('app_teacher_home');
        }

        // Nettoyage du nom de fichier
        $originalName = $file->getClientOriginalName();
        $info = pathinfo($originalName);
        $filenameOnly = $info['filename']; // nom sans extension
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filenameOnly); // on enlève caractères spéciaux
        $extension = $file->guessExtension(); // récupère l’extension réelle
        $filename = uniqid() . '-' . $safeName . '.' . $extension;

        // Déplacement du fichier dans le dossier public/uploads/video
        $file->move($this->getParameter('video_directory'), $filename);

        // Création et enregistrement de l’entité Video
        $video = new Video();
        $video->setTitle($title);
        $video->setPath($filename);
        $video->setCourse($course);

        $this->videoRepository->save($video, true);

        $this->addFlash('success', 'Vidéo ajoutée avec succès !');

        return $this->redirectToRoute('app_teacher_home');
    }

}