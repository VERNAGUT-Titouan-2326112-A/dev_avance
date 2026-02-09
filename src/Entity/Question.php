<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
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
 * Entité Question - Représente une question d'un QCM
 *
 * Cette entité gère les questions d'un QCM avec leur texte et le type de question.
 * Une question peut avoir plusieurs réponses possibles (Answer).
 * Une seule réponse est correcte.
 *
 * Groupes de sérialisation:
 * - question:read: Utilisé pour la sérialisation (lecture) des questions
 * - question:write: Utilisé pour la désérialisation (écriture) des questions
 */
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère la liste de toutes les questions'),
        new Get(description: 'Récupère une question spécifique'),
        new Post(description: 'Crée une nouvelle question'),
        new Put(description: 'Met à jour une question'),
        new Delete(description: 'Supprime une question'),
    ],
    normalizationContext: ['groups' => ['question:read']],
    denormalizationContext: ['groups' => ['question:write']]
)]
class Question
{
    /**
     * Identifiant unique de la question
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['question:read', 'question:write', 'qcm:read'])]
    private ?int $id = null;

    /**
     * Texte/libellé de la question
     * Exemple: "Quel est la capitale de la France?"
     */
    #[ORM\Column(type: 'text')]
    #[Groups(['question:read', 'question:write', 'qcm:read'])]
    private ?string $text = null;

    /**
     * Type de question
     * Valeurs: "multiple_choice", "true_false", "short_answer"
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['question:read', 'question:write', 'qcm:read'])]
    private ?string $type = null;

    /**
     * Points attribués à cette question
     * Par défaut: 1 point
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['question:read', 'question:write', 'qcm:read'])]
    private ?int $points = 1;

    /**
     * Ordre de la question dans le QCM
     * Permet de conserver l'ordre des questions
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['question:read', 'question:write', 'qcm:read'])]
    private ?int $orderQuestion = null;

    /**
     * QCM auquel cette question appartient
     * Relation "plusieurs-à-un"
     */
    #[ORM\ManyToOne(targetEntity: QCM::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['question:write'])]
    private ?QCM $qcm = null;

    /**
     * Collection des réponses possibles à cette question
     * Relation "un-à-plusieurs"
     *
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'question', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['question:read', 'question:write', 'qcm:read', 'qcm:write'])] // 👈 AJOUT DE qcm:read/write
    private Collection $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->points = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): static
    {
        $this->points = $points;
        return $this;
    }

    public function getOrderQuestion(): ?int
    {
        return $this->orderQuestion;
    }

    public function setOrderQuestion(?int $orderQuestion): static
    {
        $this->orderQuestion = $orderQuestion;
        return $this;
    }

    public function getQcm(): ?QCM
    {
        return $this->qcm;
    }

    public function setQcm(?QCM $qcm): static
    {
        $this->qcm = $qcm;
        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }
        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }
        return $this;
    }
}