<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Entité Quiz - Représente un questionnaire à choix multiples
 *
 * Cette entité gère la structure d'un Quiz avec ses propriétés (thème, note, nom)
 * et ses questions associées. Elle expose une API REST complète pour les opérations CRUD.
 *
 * Groupes de sérialisation:
 * - quiz:read: Utilisé pour la sérialisation (lecture) des Quiz
 * - quiz:write: Utilisé pour la désérialisation (écriture) des Quiz
 */
#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[ORM\Table(name: 'quiz')]
#[ApiResource(
    shortName: 'Quiz',
    operations: [
        new GetCollection(description: 'Récupère la liste de tous les Quiz'),
        new Get(description: 'Récupère un Quiz spécifique avec ses questions'),
        new Post(description: 'Crée un nouveau Quiz'),
        new Put(description: 'Met à jour un Quiz'),
        new Delete(description: 'Supprime un Quiz'),
    ],
    normalizationContext: ['groups' => ['quiz:read']],
    denormalizationContext: ['groups' => ['quiz:write']]
)]
class Quiz
{
    /**
     * Identifiant unique du Quiz
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quiz:read'])]
    private ?int $id = null;

    /**
     * Thème du Quiz (ex: "Mathématiques", "Histoire")
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read', 'quiz:write'])]
    private ?string $theme = null;

    /**
     * Score ou note maximale du Quiz
     * Valeur entière représentant le nombre de points possibles
     */
    #[ORM\Column]
    #[Groups(['quiz:read', 'quiz:write'])]
    private ?int $note = null;

    /**
     * Nom/titre du Quiz (ex: "Quiz Chapitre 3")
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read', 'quiz:write'])]
    private ?string $nom = null;

    /**
     * Course à laquelle ce Quiz appartient
     * Relation "plusieurs-à-un" : plusieurs Quiz peuvent appartenir à un même cours
     *
     * @var ?Course
     */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'qcms')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['quiz:read', 'quiz:write'])]
    private ?Course $course = null;

    /**
     * Collection des questions associées à ce Quiz
     * Une relation "un-à-plusieurs" : un Quiz peut avoir plusieurs questions
     *
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'qcm', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['quiz:read', 'quiz:write'])]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant unique du Quiz
     *
     * @return ?int L'ID du Quiz, null si non encore persisté
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le thème du Quiz
     *
     * @return ?string Le thème du Quiz
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Définit le thème du Quiz
     *
     * @param string $theme Le thème à assigner au Quiz
     * @return static Instance courante pour permettre l'appel en chaîne (fluent interface)
     */
    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Récupère la note maximale du Quiz
     *
     * @return ?int La note maximale du Quiz
     */
    public function getNote(): ?int
    {
        return $this->note;
    }

    /**
     * Définit la note maximale du Quiz
     *
     * @param int $note La note maximale à assigner
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Récupère le nom/titre du Quiz
     *
     * @return ?string Le nom du Quiz
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom/titre du Quiz
     *
     * @param string $nom Le nom à assigner au Quiz
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère toutes les questions associées à ce Quiz
     *
     * @return Collection<int, Question> Collection d'objets Question
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * Ajoute une question au Quiz
     * Évite les doublons et maintient la relation bidirectionnelle
     *
     * @param Question $question La question à ajouter
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQcm($this);
        }

        return $this;
    }

    /**
     * Retire une question du Quiz
     * Maintient la cohérence de la relation bidirectionnelle
     *
     * @param Question $question La question à retirer
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // Casse la relation inverse si cette entité était le propriétaire
            if ($question->getQcm() === $this) {
                $question->setQcm(null);
            }
        }

        return $this;
    }

    /**
     * Récupère le cours auquel ce Quiz appartient
     *
     * @return ?Course Le cours parent, null si non associé
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * Définit le cours pour ce Quiz
     *
     * @param ?Course $course Le cours à assigner, null pour dissocier
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }
}