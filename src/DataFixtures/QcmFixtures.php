<?php

namespace App\DataFixtures;

use App\Entity\QCM;
use App\Repository\CourseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * QcmFixtures
 *
 * Classe de fixtures pour charger les QCM, Questions et Réponses dans la base de données.
 * Crée plusieurs QCM avec des questions et réponses associées.
 *
 * Dépendances :
 * - CourseFixtures (pour les cours associés aux QCM)
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class QcmFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Repository pour accéder aux cours
     */
    private CourseRepository $courseRepository;

    /**
     * Constructeur de QcmFixtures
     *
     * @param CourseRepository $courseRepository Le repository pour accéder aux cours
     */
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Constantes pour les références de fixture
     * Permet de récupérer les QCM créés dans d'autres fixtures
     */
    public const QCM_SCRUM = 'qcm_scrum';
    public const QCM_AGILE = 'qcm_agile';
    public const QCM_KANBAN = 'qcm_kanban';

    /**
     * Spécifie les fixtures dont cette fixture dépend.
     * CourseFixtures doit être chargée avant QcmFixtures.
     *
     * @return array<int, string> Tableau des classes de fixtures dépendantes
     */
    public function getDependencies(): array
    {
        return [CourseFixtures::class];
    }

    /**
     * Charge tous les QCM en base de données.
     *
     * Crée 3 QCM :
     * - QCM SCRUM
     * - QCM AGILE
     * - QCM KANBAN
     *
     * Note: Les réponses sont créées séparément par ResponseFixtures.
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Récupère les cours via le repository
        $courseAgile = $this->courseRepository->findOneBy(['title' => 'Agile & Scrum']);
        $courseKanban = $this->courseRepository->findOneBy(['title' => 'Kanban & Lean']);

        // Crée le QCM SCRUM
        $qcmScrum = new QCM();
        $qcmScrum->setNom('Quiz Scrum');
        $qcmScrum->setTheme('Agile - Scrum');
        $qcmScrum->setNote(10);
        $qcmScrum->setCourse($courseAgile);
        $manager->persist($qcmScrum);
        $this->addReference(self::QCM_SCRUM, $qcmScrum);

        // Crée le QCM AGILE
        $qcmAgile = new QCM();
        $qcmAgile->setNom('Quiz Agile');
        $qcmAgile->setTheme('Méthodologies Agiles');
        $qcmAgile->setNote(8);
        $qcmAgile->setCourse($courseAgile);
        $manager->persist($qcmAgile);
        $this->addReference(self::QCM_AGILE, $qcmAgile);

        // Crée le QCM KANBAN
        $qcmKanban = new QCM();
        $qcmKanban->setNom('Quiz Kanban');
        $qcmKanban->setTheme('Kanban - Lean');
        $qcmKanban->setNote(7);
        $qcmKanban->setCourse($courseKanban);

        $manager->persist($qcmKanban);
        $this->addReference(self::QCM_KANBAN, $qcmKanban);

        // Persiste tous les QCM en base de données
        $manager->flush();
    }
}
