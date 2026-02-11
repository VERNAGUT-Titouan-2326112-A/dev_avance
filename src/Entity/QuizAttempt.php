<?php

namespace App\Entity;

use App\Repository\QuizAttemptRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QuizAttemptRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
    ],
    normalizationContext: ['groups' => ['attempt:read', 'quiz:read']],
    denormalizationContext: ['groups' => ['attempt:write']]
)]
class QuizAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attempt:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?Quiz $qcm = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?string $answers = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?int $score = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['attempt:read'])]
    private ?\DateTimeImmutable $submittedAt = null;

    public function __construct()
    {
        $this->submittedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getStudent(): ?Student { return $this->student; }
    public function setStudent(?Student $student): static { $this->student = $student; return $this; }

    public function getQcm(): ?Quiz { return $this->qcm; }
    public function setQcm(?Quiz $qcm): static { $this->qcm = $qcm; return $this; }

    public function getAnswers(): ?string { return $this->answers; }
    public function setAnswers(?string $answers): static { $this->answers = $answers; return $this; }

    public function getScore(): ?int { return $this->score; }
    public function setScore(?int $score): static { $this->score = $score; return $this; }

    public function getSubmittedAt(): ?\DateTimeImmutable { return $this->submittedAt; }
    public function setSubmittedAt(?\DateTimeImmutable $submittedAt): static { $this->submittedAt = $submittedAt; return $this; }
}