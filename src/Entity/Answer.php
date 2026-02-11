<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Entité Answer - Représente une réponse possible à une question Quiz
 *
 * Cette entité gère les réponses (correctes ou incorrectes) proposées pour chaque question.
 * Chaque réponse a un texte et un flag indiquant si elle est correcte.
 *
 * Groupes de sérialisation:
 * - answer:read: Utilisé pour la sérialisation (lecture) des réponses
 * - answer:write: Utilisé pour la désérialisation (écriture) des réponses
 */
#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère la liste de toutes les réponses'),
        new Get(description: 'Récupère une réponse spécifique'),
        new Post(description: 'Crée une nouvelle réponse'),
        new Put(description: 'Met à jour une réponse'),
        new Delete(description: 'Supprime une réponse'),
    ],
    normalizationContext: ['groups' => ['answer:read']],
    denormalizationContext: ['groups' => ['answer:write']]
)]
class Answer
{
    /**
     * Identifiant unique de la réponse
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['answer:read', 'answer:write', 'question:read', 'question:write', 'quiz:read', 'quiz:write'])]
    private ?int $id = null;

    /**
     * Texte/contenu de la réponse
     * Exemple: "Paris", "Madrid", "Londres"
     */
    #[ORM\Column(type: 'text')]
    #[Groups(['answer:read', 'answer:write', 'question:read', 'question:write', 'quiz:read', 'quiz:write'])]
    private ?string $text = null;

    /**
     * Indicateur de correction
     * true = réponse correcte, false = réponse incorrecte
     */
    #[ORM\Column]
    #[Groups(['answer:read', 'answer:write', 'question:read', 'question:write', 'quiz:read', 'qcm:read', 'quiz:write'])]
    private bool $isCorrect = false;

    /**
     * Ordre d'affichage de la réponse
     * Permet de conserver l'ordre des réponses
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['answer:read', 'answer:write', 'question:read', 'question:write', 'quiz:read', 'quiz:write'])]
    private ?int $orderAnswer = null;

    /**
     * Question à laquelle cette réponse appartient
     * Relation "plusieurs-à-un"
     */
    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['answer:write'])]
    private ?Question $question = null;

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

    // C'EST ICI LA CORRECTION : Ajout des groupes sur le getter pour forcer la lecture
    #[Groups(['answer:read', 'answer:write', 'question:read', 'question:write', 'quiz:read', 'qcm:read', 'quiz:write'])]
    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function setCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;
        return $this;
    }

    public function getOrderAnswer(): ?int
    {
        return $this->orderAnswer;
    }

    public function setOrderAnswer(?int $orderAnswer): static
    {
        $this->orderAnswer = $orderAnswer;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;
        return $this;
    }
}