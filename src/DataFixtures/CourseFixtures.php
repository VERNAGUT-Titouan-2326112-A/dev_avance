<?php

namespace App\DataFixtures;

use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * CourseFixtures
 *
 * Classe de fixtures pour charger les cours dans la base de données.
 * Crée plusieurs cours avec des descriptions complètes.
 *
 * @package App\DataFixtures
 * @author Équipe de Développement
 */
class CourseFixtures extends Fixture
{
    /**
     * Constantes pour les références de fixture
     * Permet de récupérer les cours créés dans d'autres fixtures
     */
    public const COURSE_AGILE = 'course_agile';
    public const COURSE_KANBAN = 'course_kanban';
    public const COURSE_DEVOPS = 'course_devops';

    /**
     * Charge tous les cours en base de données.
     *
     * Crée 3 cours :
     * - Agile & Scrum
     * - Kanban & Lean
     * - DevOps
     *
     * @param ObjectManager $manager Le gestionnaire Doctrine pour persister les entités
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Crée le cours Agile & Scrum
        $courseAgile = new Course();
        $courseAgile->setTitle('Agile & Scrum');
        $courseAgile->setDescription('Apprenez les méthodologies Agile et Scrum pour gérer vos projets de manière flexible et itérative.');
        $courseAgile->setSubject('Méthodologies');
        $courseAgile->setLevel('Débutant');
        $manager->persist($courseAgile);
        $this->addReference(self::COURSE_AGILE, $courseAgile);

        // Crée le cours Kanban & Lean
        $courseKanban = new Course();
        $courseKanban->setTitle('Kanban & Lean');
        $courseKanban->setDescription('Découvrez comment optimiser votre flux de travail avec les principes Kanban et Lean.');
        $courseKanban->setSubject('Méthodologies');
        $courseKanban->setLevel('Intermédiaire');
        $manager->persist($courseKanban);
        $this->addReference(self::COURSE_KANBAN, $courseKanban);

        // Crée le cours DevOps
        $courseDevOps = new Course();
        $courseDevOps->setTitle('DevOps - Principes et Pratiques');
        $courseDevOps->setDescription('Comprenez les principes du DevOps et apprenez à intégrer le développement et les opérations.');
        $courseDevOps->setSubject('DevOps');
        $courseDevOps->setLevel('Avancé');
        $manager->persist($courseDevOps);
        $this->addReference(self::COURSE_DEVOPS, $courseDevOps);

        // Persiste tous les cours en base de données
        $manager->flush();
    }
}
