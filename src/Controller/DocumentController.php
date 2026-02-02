<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DocumentController extends AbstractController
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    #[Route('/add_pdf', name: 'add_pdf')]
    public function index(Request $request): Response
    {
        $file = $request->files->get('pdfFile');
        $title = $request->request->get('pdfTitle');

        if ($file && $file->getMimeType() === 'application/pdf') {
            $filename = uniqid() . '-' . $file->getClientOriginalName();
            $file->move($this->getParameter('pdf_directory'), $filename);

            $pdf = new Document();
            $pdf->setTitle($title);
            $pdf->setPath($filename);

            $this->documentRepository->save($pdf, true);

            $this->addFlash('success', 'Document ajouté avec succès !');
        } else {
            $this->addFlash('error', 'Fichier invalide.');
        }
        $pdfs = $this->documentRepository->findAll();
        return $this->render('User/Professor/index.html.twig', [
            'pdfs' => $pdfs,
        ]);
    }
}