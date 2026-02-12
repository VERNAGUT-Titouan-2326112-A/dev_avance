<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Document;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Video;
use App\Service\QuizGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * QuizController
 *
 * Contrôleur responsable de la génération et de la prévisualisation
 * des Quiz à partir :
 * - d’un Document (PDF)
 * - d’une Vidéo
 *
 * Ce contrôleur exploite un service d’intelligence artificielle
 * (QuizGeneratorService) afin de générer automatiquement :
 * - un Quiz
 * - ses Questions
 * - ses Réponses
 *
 * Il permet également :
 * - la prévisualisation du quiz
 * - la simulation d’une correction
 *
 * @package App\Controller
 */
class QuizController extends AbstractController
{
    /**
     * Génération d’un Quiz à partir d’un Document PDF.
     *
     * Route : /teacher/quiz/generate/{id}
     * Méthode : POST
     *
     * Processus :
     * 1. Récupération du nombre de questions et du type (MCQ par défaut)
     * 2. Vérification que le document possède un fichier valide
     * 3. Appel du service IA pour générer les données du quiz
     * 4. Création du Quiz en base
     * 5. Création des Questions associées
     * 6. Création des Answers associées
     * 7. Enregistrement en base (flush)
     * 8. Message Flash de succès ou d’erreur
     */
    #[Route('/teacher/quiz/generate/{id}', name: 'app_quiz_generate', methods: ['POST'])]
    public function generate(
        Request $request,
        Document $document,
        QuizGeneratorService $quizGen,
        EntityManagerInterface $em
    ): Response
    {
        $nbQuestions = (int) $request->request->get('nbQuestions', 10);
        $type = $request->request->get('type', 'mcq');

        // Vérifie que le document possède un fichier PDF associé
        if (!$document->getPath()) {
            $this->addFlash('error', 'Document sans fichier PDF.');
            return $this->redirectToRoute('app_teacher_home');
        }

        try {
            // Appel du service IA
            $data = $quizGen->generateFromDocument($document->getPath(), $nbQuestions, $type);

            // Création du Quiz principal
            $quiz = new Quiz();
            $quiz->setNom($data['title'] ?? 'Quiz généré : ' . $document->getTitle());
            $quiz->setTheme($document->getTitle());
            $quiz->setNote(20);
            $quiz->setCourse($document->getCourse());

            $em->persist($quiz);

            // Création des Questions et Answers
            $qIndex = 1;

            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {

                    $question = new Question();
                    $question->setText($qData['text']);
                    $question->setPoints($qData['points'] ?? 1);
                    $question->setType($qData['type'] ?? $type);
                    $question->setOrderQuestion($qIndex++);
                    $question->setQcm($quiz);

                    $em->persist($question);

                    $aIndex = 1;

                    if (isset($qData['answers']) && is_array($qData['answers'])) {
                        foreach ($qData['answers'] as $aData) {

                            $answer = new Answer();
                            $answer->setText($aData['text']);
                            $answer->setCorrect($aData['isCorrect']);
                            $answer->setOrderAnswer($aIndex++);
                            $answer->setQuestion($question);

                            $em->persist($answer);
                        }
                    }
                }
            }

            $em->flush();

            $this->addFlash('success', 'Le Quiz a été généré avec succès !');

        } catch (\Exception $e) {

            // Gestion des erreurs IA
            $this->addFlash('error', 'Erreur IA : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_teacher_home');
    }

    /**
     * Génération d’un Quiz à partir d’une Vidéo.
     *
     * Même logique que la génération depuis un PDF,
     * mais en appelant generateFromVideo().
     */
    #[Route('/teacher/quiz/generate-video/{id}', name: 'app_quiz_generate_video', methods: ['POST'])]
    public function generateVideo(
        Request $request,
        Video $video,
        QuizGeneratorService $quizGen,
        EntityManagerInterface $em
    ): Response
    {
        $nbQuestions = (int) $request->request->get('nbQuestions', 10);
        $type = $request->request->get('type', 'mcq');

        if (!$video->getPath()) {
            $this->addFlash('error', 'Vidéo sans fichier associé.');
            return $this->redirectToRoute('app_teacher_home');
        }

        try {
            $data = $quizGen->generateFromVideo($video->getPath(), $nbQuestions, $type);

            $quiz = new Quiz();
            $quiz->setNom($data['title'] ?? 'Quiz Vidéo : ' . $video->getTitle());
            $quiz->setTheme($video->getTitle());
            $quiz->setNote(20);
            $quiz->setCourse($video->getCourse());

            $em->persist($quiz);

            $qIndex = 1;

            if (isset($data['questions'])) {
                foreach ($data['questions'] as $qData) {

                    $question = new Question();
                    $question->setText($qData['text']);
                    $question->setPoints($qData['points'] ?? 1);
                    $question->setType($qData['type'] ?? $type);
                    $question->setOrderQuestion($qIndex++);
                    $question->setQcm($quiz);

                    $em->persist($question);

                    $aIndex = 1;

                    foreach ($qData['answers'] ?? [] as $aData) {

                        $answer = new Answer();
                        $answer->setText($aData['text']);
                        $answer->setCorrect($aData['isCorrect']);
                        $answer->setOrderAnswer($aIndex++);
                        $answer->setQuestion($question);

                        $em->persist($answer);
                    }
                }
            }

            $em->flush();

            $this->addFlash('success', 'Le Quiz vidéo a été généré avec succès !');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur IA : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_teacher_home');
    }

    /**
     * Prévisualisation d’un Quiz généré.
     *
     * Permet au professeur de visualiser le quiz
     * avant publication ou utilisation.
     */
    #[Route('/teacher/quiz/preview/{id}', name: 'app_teacher_quiz_preview')]
    public function preview(Quiz $quiz): Response
    {
        return $this->render('User/Professor/preview_quiz.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * Simulation de soumission d’un Quiz (prévisualisation).
     *
     * Cette méthode :
     * - Calcule le score
     * - Compare les réponses sélectionnées
     * - Prépare un tableau de correction
     * - Affiche le résultat
     *
     * Aucun enregistrement en base n’est effectué ici.
     */
    #[Route('/teacher/quiz/preview/submit/{id}', name: 'app_teacher_quiz_preview_submit', methods: ['POST'])]
    public function previewSubmit(Quiz $quiz, Request $request, EntityManagerInterface $em): Response
    {
        $score = 0;
        $totalPoints = 0;
        $userAnswers = $request->request->all('answers');
        $correction = [];

        foreach ($quiz->getQuestions() as $question) {

            $points = $question->getPoints();
            $totalPoints += $points;

            $questionId = $question->getId();
            $isCorrect = false;
            $userAnswerId = null;

            if (isset($userAnswers[$questionId])) {

                $userAnswerId = $userAnswers[$questionId];
                $selectedAnswer = $em->getRepository(Answer::class)->find($userAnswerId);

                if ($selectedAnswer && $selectedAnswer->isCorrect()) {
                    $score += $points;
                    $isCorrect = true;
                }
            }

            $correction[$questionId] = [
                'isCorrect' => $isCorrect,
                'userAnswerId' => $userAnswerId,
            ];
        }

        return $this->render('User/Professor/preview_result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'totalPoints' => $totalPoints,
            'correction' => $correction
        ]);
    }
}
