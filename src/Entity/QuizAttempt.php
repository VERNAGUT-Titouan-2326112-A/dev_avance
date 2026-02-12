<?php

namespace App\Entity;

use App\Repository\QuizAttemptRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Entité QuizAttempt - Représente une tentative d’un étudiant sur un Quiz
 *
 * Cette entité enregistre les informations liées à la participation
 * d’un étudiant à un Quiz : les réponses soumises, le score obtenu
 * ainsi que la date de soumission.
 *
 * Groupes de sérialisation :
 * - attempt:read  → utilisé lors de la lecture (GET)
 * - attempt:write → utilisé lors de l’écriture (POST)
 */
#[ORM\Entity(repositoryClass: QuizAttemptRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(), // Récupère toutes les tentatives
        new Get(),           // Récupère une tentative spécifique
        new Post(),          // Crée une nouvelle tentative
    ],
    normalizationContext: ['groups' => ['attempt:read', 'quiz:read']],
    denormalizationContext: ['groups' => ['attempt:write']]
)]
class QuizAttempt
{
    /**
     * Identifiant unique de la tentative
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['attempt:read'])]
    private ?int $id = null;

    /**
     * Étudiant ayant effectué la tentative
     * Relation plusieurs-à-un :
     * Plusieurs tentatives peuvent appartenir au même étudiant
     */
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?Student $student = null;

    /**
     * Quiz concerné par la tentative
     * Relation plusieurs-à-un :
     * Plusieurs tentatives peuvent concerner le même Quiz
     */
    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?Quiz $qcm = null;

    /**
     * Réponses soumises par l’étudiant
     * Stockées au format texte (souvent JSON encodé)
     * Nullable si les réponses ne sont pas encore enregistrées
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?string $answers = null;

    /**
     * Score obtenu par l’étudiant
     * Valeur entière représentant la note calculée
     * Peut être null si la correction n’a pas encore été faite
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['attempt:read', 'attempt:write'])]
    private ?int $score = null;

    /**
     * Date et heure de soumission de la tentative
     * Initialisée automatiquement lors de la création
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['attempt:read'])]
    private ?\DateTimeImmutable $submittedAt = null;

    /**
     * Constructeur
     * Initialise automatiquement la date de soumission
     */
    public function __construct()
    {
        $this->submittedAt = new \DateTimeImmutable();
    }

    /**
     * Récupère l’identifiant unique de la tentative
     *
     * @return ?int ID de la tentative
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l’étudiant associé à cette tentative
     *
     * @return ?Student
     */
    public function getStudent(): ?Student
    {
        return $this->student;
    }

    /**
     * Définit l’étudiant associé à cette tentative
     *
     * @param ?Student $student
     * @return static
     */
    public function setStudent(?Student $student): static
    {
        $this->student = $student;
        return $this;
    }

    /**
     * Récupère le Quiz concerné
     *
     * @return ?Quiz
     */
    public function getQcm(): ?Quiz
    {
        return $this->qcm;
    }

    /**
     * Définit le Quiz concerné
     *
     * @param ?Quiz $qcm
     * @return static
     */
    public function setQcm(?Quiz $qcm): static
    {
        $this->qcm = $qcm;
        return $this;
    }

    /**
     * Récupère les réponses soumises
     *
     * @return ?string
     */
    public function getAnswers(): ?string
    {
        return $this->answers;
    }

    /**
     * Définit les réponses soumises
     *
     * @param ?string $answers
     * @return static
     */
    public function setAnswers(?string $answers): static
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * Récupère le score obtenu
     *
     * @return ?int
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    /**
     * Définit le score obtenu
     *
     * @param ?int $score
     * @return static
     */
    public function setScore(?int $score): static
    {
        $this->score = $score;
        return $this;
    }

    /**
     * Récupère la date de soumission
     *
     * @return ?\DateTimeImmutable
     */
    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    /**
     * Définit la date de soumission
     *
     * @param ?\DateTimeImmutable $submittedAt
     * @return static
     */
    public function setSubmittedAt(?\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }
}
