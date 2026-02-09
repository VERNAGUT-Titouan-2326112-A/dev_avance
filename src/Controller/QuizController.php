<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Document;
use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Video;
use App\Service\QuizGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(): Response
    {
        return $this->render('quiz/index.html.twig', [
            'controller_name' => 'QuizController',
        ]);
    }

    #[Route('/teacher/quiz/generate/{id}', name: 'app_quiz_generate')]
    public function generate(
        Document $document,
        QuizGeneratorService $quizGen,
        EntityManagerInterface $em
    ): Response
    {
        if (!$document->getPath()) {
            $this->addFlash('error', 'Document sans fichier PDF.');
            return $this->redirectToRoute('app_teacher_home');
        }

        try {
            $data = $quizGen->generateFromDocument($document->getPath());

            $quiz = new Quiz();

            if (method_exists($quiz, 'setNom')) {
                $quiz->setNom($data['title'] ?? 'Quiz généré : ' . $document->getTitle());
            } elseif (method_exists($quiz, 'setTitle')) {
                $quiz->setTitle($data['title'] ?? 'Quiz généré : ' . $document->getTitle());
            }

            $quiz->setTheme($document->getTitle());
            $quiz->setNote(20);
            $quiz->setCourse($document->getCourse());

            $em->persist($quiz);

            $qIndex = 1;

            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {
                    $question = new Question();
                    $question->setText($qData['text']);
                    $question->setPoints($qData['points'] ?? 1);
                    $question->setType($qData['type'] ?? 'multiple_choice');
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

            $this->addFlash('success', 'Le Quiz a été généré et enregistré dans la base de données !');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur IA : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_teacher_home');
    }

    #[Route('/teacher/quiz/generate-video/{id}', name: 'app_quiz_generate_video')]
    public function generateVideo(
        Video $video,
        QuizGeneratorService $quizGen,
        EntityManagerInterface $em
    ): Response
    {
        if (!$video->getPath()) {
            $this->addFlash('error', 'Vidéo sans fichier associé.');
            return $this->redirectToRoute('app_teacher_home');
        }

        try {
            $data = $quizGen->generateFromVideo($video->getPath());

            $quiz = new Quiz();

            if (method_exists($quiz, 'setNom')) {
                $quiz->setNom($data['title'] ?? 'Quiz Vidéo : ' . $video->getTitle());
            } elseif (method_exists($quiz, 'setTitle')) {
                $quiz->setTitle($data['title'] ?? 'Quiz Vidéo : ' . $video->getTitle());
            }

            $quiz->setTheme($video->getTitle());
            $quiz->setNote(20);
            $quiz->setCourse($video->getCourse());

            $em->persist($quiz);

            $qIndex = 1;

            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {
                    $question = new Question();
                    $question->setText($qData['text']);
                    $question->setPoints($qData['points'] ?? 1);
                    $question->setType($qData['type'] ?? 'multiple_choice');
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

            $this->addFlash('success', 'Le Quiz vidéo a été généré avec succès !');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur IA : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_teacher_home');
    }
}