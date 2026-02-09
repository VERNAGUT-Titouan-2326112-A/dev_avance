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

        if (!$courseId) {
            $this->addFlash('error', 'Aucun cours sélectionné.');
            return $this->redirectToRoute('app_teacher_home');
        }

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

        return $this->redirectToRoute('app_teacher_home');
    }

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

        if (!str_starts_with($file->getMimeType(), 'video/')) {
            $this->addFlash('error', 'Le fichier doit être une vidéo.');
            return $this->redirectToRoute('app_teacher_home');
        }

        $originalName = $file->getClientOriginalName();
        $info = pathinfo($originalName);
        $filenameOnly = $info['filename'];
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filenameOnly);
        $extension = $file->guessExtension();
        $filename = uniqid() . '-' . $safeName . '.' . $extension;

        $file->move($this->getParameter('video_directory'), $filename);

        $video = new Video();
        $video->setTitle($title);
        $video->setPath($filename);
        $video->setCourse($course);

        $this->videoRepository->save($video, true);

        $this->addFlash('success', 'Vidéo ajoutée avec succès !');

        return $this->redirectToRoute('app_teacher_home');
    }
}