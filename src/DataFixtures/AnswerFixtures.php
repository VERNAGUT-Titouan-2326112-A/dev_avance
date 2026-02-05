<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QCM;
use App\Repository\QCMRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * AnswerFixtures (Anciennement ResponseFixtures)
 *
 * Classe de fixtures pour charger les questions et réponses (Answer) dans la base de données.
 * Crée les questions et leurs réponses associées pour chaque QCM.
 *
 * Dépendances :
 * - QcmFixtures (pour les QCM associés aux questions/réponses)
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class AnswerFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Repository pour accéder aux QCM
     */
    private QCMRepository $qcmRepository;

    /**
     * Constructeur de AnswerFixtures
     *
     * @param QCMRepository $qcmRepository Le repository pour accéder aux QCM
     */
    public function __construct(QCMRepository $qcmRepository)
    {
        $this->qcmRepository = $qcmRepository;
    }

    /**
     * Spécifie les fixtures dont cette fixture dépend.
     * QcmFixtures doit être chargée avant celle-ci.
     *
     * @return array<int, string> Tableau des classes de fixtures dépendantes
     */
    public function getDependencies(): array
    {
        return [QcmFixtures::class];
    }

    /**
     * Charge toutes les questions et réponses en base de données.
     *
     * Crée les questions/réponses pour chaque QCM :
     * - Réponses pour QCM SCRUM
     * - Réponses pour QCM AGILE
     * - Réponses pour QCM KANBAN
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Récupère les QCM
        $qcmScrum = $this->qcmRepository->findOneBy(['nom' => 'Quiz Scrum']);
        $qcmAgile = $this->qcmRepository->findOneBy(['nom' => 'Quiz Agile']);
        $qcmKanban = $this->qcmRepository->findOneBy(['nom' => 'Quiz Kanban']);

        // ===== RÉPONSES POUR QCM SCRUM =====
        if ($qcmScrum) {
            // Question 1: "Qu'est-ce qu'un Sprint dans Scrum?"
            $this->createQuestionWithAnswers($manager, $qcmScrum, "Qu'est-ce qu'un Sprint dans Scrum?", [
                ['text' => 'Une période de 1 à 4 semaines', 'isCorrect' => true],
                ['text' => 'Un type de réunion', 'isCorrect' => false],
                ['text' => 'Un outil de développement', 'isCorrect' => false]
            ]);

            // Question 2: "Qui est responsable du Product Backlog?"
            $this->createQuestionWithAnswers($manager, $qcmScrum, "Qui est responsable du Product Backlog?", [
                ['text' => 'Le Product Owner', 'isCorrect' => true],
                ['text' => 'Le Scrum Master', 'isCorrect' => false],
                ['text' => 'L\'équipe de développement', 'isCorrect' => false]
            ]);

            // Question 3: "Combien de temps dure généralement une Daily Standup?"
            $this->createQuestionWithAnswers($manager, $qcmScrum, "Combien de temps dure généralement une Daily Standup?", [
                ['text' => 'Maximum 15 minutes', 'isCorrect' => true],
                ['text' => 'Maximum 30 minutes', 'isCorrect' => false],
                ['text' => 'Maximum 1 heure', 'isCorrect' => false]
            ]);
        }

        // ===== RÉPONSES POUR QCM AGILE =====
        if ($qcmAgile) {
            // Question 1: "Quel est le principe fondamental d'Agile?"
            $this->createQuestionWithAnswers($manager, $qcmAgile, "Quel est le principe fondamental d'Agile?", [
                ['text' => 'Valoriser les individus et les interactions', 'isCorrect' => true],
                ['text' => 'Valoriser les processus et les outils', 'isCorrect' => false],
                ['text' => 'Valoriser la documentation', 'isCorrect' => false]
            ]);

            // Question 2: "Qu'est-ce qu'une User Story?"
            $this->createQuestionWithAnswers($manager, $qcmAgile, "Qu'est-ce qu'une User Story?", [
                ['text' => 'Une description d\'une fonctionnalité du point de vue utilisateur', 'isCorrect' => true],
                ['text' => 'Une histoire personnelle du développeur', 'isCorrect' => false],
                ['text' => 'Un rapport de bug', 'isCorrect' => false]
            ]);

            // Question 3: "Quel est l'objectif du refactoring?"
            $this->createQuestionWithAnswers($manager, $qcmAgile, "Quel est l'objectif du refactoring?", [
                ['text' => 'Améliorer la qualité du code sans changer sa fonctionnalité', 'isCorrect' => true],
                ['text' => 'Ajouter de nouvelles fonctionnalités', 'isCorrect' => false],
                ['text' => 'Corriger les bugs', 'isCorrect' => false]
            ]);
        }

        // ===== RÉPONSES POUR QCM KANBAN =====
        if ($qcmKanban) {
            // Question 1: "Qu'est-ce que Kanban?"
            $this->createQuestionWithAnswers($manager, $qcmKanban, "Qu'est-ce que Kanban?", [
                ['text' => 'Un système de gestion du flux de travail', 'isCorrect' => true],
                ['text' => 'Un langage de programmation', 'isCorrect' => false],
                ['text' => 'Une méthode de documentation', 'isCorrect' => false]
            ]);

            // Question 2: "Quel est le principal avantage du Kanban?"
            $this->createQuestionWithAnswers($manager, $qcmKanban, "Quel est le principal avantage du Kanban?", [
                ['text' => 'Visualiser et limiter le travail en cours', 'isCorrect' => true],
                ['text' => 'Augmenter le travail en cours', 'isCorrect' => false],
                ['text' => 'Éliminer les réunions', 'isCorrect' => false]
            ]);
        }

        // Persiste toutes les données en base
        $manager->flush();
    }

    /**
     * Crée une Question et ses Réponses associées.
     * (Remplace l'ancienne méthode createResponse pour s'adapter à la nouvelle structure)
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine
     * @param QCM $qcm Le QCM auquel la question appartient
     * @param string $questionText Le libellé de la question
     * @param array $answersData Tableau des réponses [['text' => '...', 'isCorrect' => bool], ...]
     *
     * @return void
     */
    private function createQuestionWithAnswers(ObjectManager $manager, QCM $qcm, string $questionText, array $answersData): void
    {
        // 1. Création de la Question
        $question = new Question();
        $question->setText($questionText);
        $question->setType('multiple_choice'); // Type par défaut
        $question->setPoints(1); // Points par défaut
        $question->setQcm($qcm);

        $manager->persist($question);

        // 2. Création des Réponses liées à cette Question
        foreach ($answersData as $data) {
            $answer = new Answer();
            $answer->setText($data['text']);
            $answer->setCorrect($data['isCorrect']);
            $answer->setQuestion($question);

            $manager->persist($answer);
        }
    }
}