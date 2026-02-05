<?php

namespace App\Entity;

use App\Repository\QCMRepository;
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
 * Entité QCM - Représente un questionnaire à choix multiples
 *
 * Cette entité gère la structure d'un QCM avec ses propriétés (thème, note, nom)
 * et ses questions associées. Elle expose une API REST complète pour les opérations CRUD.
 *
 * Groupes de sérialisation:
 * - qcm:read: Utilisé pour la sérialisation (lecture) des QCM
 * - qcm:write: Utilisé pour la désérialisation (écriture) des QCM
 */
#[ORM\Entity(repositoryClass: QCMRepository::class)]
#[ORM\Table(name: 'qcm')]
#[ApiResource(
    shortName: 'qcm',
    operations: [
        new GetCollection(description: 'Récupère la liste de tous les QCM'),
        new Get(description: 'Récupère un QCM spécifique avec ses questions'),
        new Post(description: 'Crée un nouveau QCM'),
        new Put(description: 'Met à jour un QCM'),
        new Delete(description: 'Supprime un QCM'),
    ],
    normalizationContext: ['groups' => ['qcm:read']],
    denormalizationContext: ['groups' => ['qcm:write']]
)]
class QCM
{
    /**
     * Identifiant unique du QCM
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['qcm:read'])]
    private ?int $id = null;

    /**
     * Thème du QCM (ex: "Mathématiques", "Histoire")
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['qcm:read', 'qcm:write'])]
    private ?string $theme = null;

    /**
     * Score ou note maximale du QCM
     * Valeur entière représentant le nombre de points possibles
     */
    #[ORM\Column]
    #[Groups(['qcm:read', 'qcm:write'])]
    private ?int $note = null;

    /**
     * Nom/titre du QCM (ex: "Quiz Chapitre 3")
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['qcm:read', 'qcm:write'])]
    private ?string $nom = null;

    /**
     * Course à laquelle ce QCM appartient
     * Relation "plusieurs-à-un" : plusieurs QCM peuvent appartenir à un même cours
     *
     * @var ?Course
     */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'qcms')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['qcm:read', 'qcm:write'])]
    private ?Course $course = null;

    /**
     * Collection des questions associées à ce QCM
     * Une relation "un-à-plusieurs" : un QCM peut avoir plusieurs questions
     *
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'qcm', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['qcm:read', 'qcm:write'])]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant unique du QCM
     *
     * @return ?int L'ID du QCM, null si non encore persisté
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le thème du QCM
     *
     * @return ?string Le thème du QCM
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * Définit le thème du QCM
     *
     * @param string $theme Le thème à assigner au QCM
     * @return static Instance courante pour permettre l'appel en chaîne (fluent interface)
     */
    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Récupère la note maximale du QCM
     *
     * @return ?int La note maximale du QCM
     */
    public function getNote(): ?int
    {
        return $this->note;
    }

    /**
     * Définit la note maximale du QCM
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
     * Récupère le nom/titre du QCM
     *
     * @return ?string Le nom du QCM
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom/titre du QCM
     *
     * @param string $nom Le nom à assigner au QCM
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Récupère toutes les questions associées à ce QCM
     *
     * @return Collection<int, Question> Collection d'objets Question
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * Ajoute une question au QCM
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
     * Retire une question du QCM
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
     * Récupère le cours auquel ce QCM appartient
     *
     * @return ?Course Le cours parent, null si non associé
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * Définit le cours pour ce QCM
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