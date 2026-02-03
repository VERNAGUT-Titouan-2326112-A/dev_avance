<?php

namespace App\Entity;

use App\Repository\QuizAttemptRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité QuizAttempt - Tentative de réponse à un QCM
 * Enregistre l'historique des réponses des étudiants aux QCM avec les scores
 */
#[ORM\Entity(repositoryClass: QuizAttemptRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère l\'historique des tentatives de QCM'),
        new Get(description: 'Récupère les détails d\'une tentative'),
        new Post(description: 'Soumet les réponses d\'un QCM'),
        new Put(description: 'Met à jour une tentative'),
        new Delete(description: 'Supprime une tentative'),
    ]
)]
class QuizAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: QCM::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?QCM $qcm = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answers = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $submittedAt = null;

    public function __construct()
    {
        $this->submittedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;
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

    public function getAnswers(): ?string
    {
        return $this->answers;
    }

    public function setAnswers(?string $answers): static
    {
        $this->answers = $answers;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(?\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }
}
