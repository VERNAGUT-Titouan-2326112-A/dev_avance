<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Entité Video - Représente une vidéo associée à un cours
 *
 * Cette entité permet de stocker les informations d’une vidéo pédagogique
 * liée à un cours : son titre, son chemin d’accès (fichier ou URL)
 * ainsi que le cours auquel elle appartient.
 *
 * Elle expose une API REST complète permettant les opérations CRUD.
 *
 * Groupes de sérialisation :
 * - video:read  → utilisé lors de la lecture des vidéos (GET)
 * - video:write → utilisé lors de la création/modification (POST, PUT)
 */
#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(), // Récupère toutes les vidéos
        new Get(),           // Récupère une vidéo spécifique
        new Post(),          // Crée une nouvelle vidéo
        new Put(),           // Met à jour une vidéo existante
        new Delete(),        // Supprime une vidéo
    ],
    normalizationContext: ['groups' => ['video:read']],
    denormalizationContext: ['groups' => ['video:write']]
)]
class Video
{
    /**
     * Identifiant unique de la vidéo
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['video:read', 'course:read', 'course:write'])]
    private ?int $id = null;

    /**
     * Titre de la vidéo
     * Chaîne de caractères limitée à 255 caractères
     * Exemple : "Introduction aux API REST"
     */
    #[ORM\Column(length: 255)]
    #[Groups(['video:read', 'video:write', 'course:read', 'course:write'])]
    private ?string $title = null;

    /**
     * Chemin ou URL de la vidéo
     * Peut contenir un chemin local ou un lien externe (YouTube, serveur, etc.)
     * Limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['video:read', 'video:write', 'course:read', 'course:write'])]
    private ?string $path = null;

    /**
     * Cours auquel cette vidéo est associée
     * Relation plusieurs-à-un :
     * Plusieurs vidéos peuvent appartenir à un même cours
     */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['video:write'])]
    private ?Course $course = null;

    /**
     * Récupère l’identifiant unique de la vidéo
     *
     * @return ?int ID de la vidéo
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre de la vidéo
     *
     * @return ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la vidéo
     *
     * @param string $title
     * @return static Instance courante (fluent interface)
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Récupère le chemin ou l’URL de la vidéo
     *
     * @return ?string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Définit le chemin ou l’URL de la vidéo
     *
     * @param string $path
     * @return static Instance courante
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Récupère le cours associé à cette vidéo
     *
     * @return ?Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * Définit le cours associé à cette vidéo
     *
     * @param ?Course $course
     * @return static Instance courante
     */
    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }
}