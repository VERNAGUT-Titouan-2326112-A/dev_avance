<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Entité Document - Représente un document pédagogique associé à un cours
 *
 * Cette entité permet de stocker les informations relatives à un document
 * mis à disposition dans un cours (PDF, support de cours, fiche TD, etc.).
 *
 * Chaque document possède un titre, un chemin d’accès (fichier ou URL)
 * et est obligatoirement associé à un cours.
 *
 * Elle expose une API REST complète pour les opérations CRUD.
 *
 * Groupes de sérialisation :
 * - document:read  → utilisé lors de la lecture (GET)
 * - document:write → utilisé lors de la création/modification (POST, PUT)
 */
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(), // Récupère tous les documents
        new Get(),           // Récupère un document spécifique
        new Post(),          // Crée un nouveau document
        new Put(),           // Met à jour un document existant
        new Delete(),        // Supprime un document
    ],
    normalizationContext: ['groups' => ['document:read']],
    denormalizationContext: ['groups' => ['document:write']]
)]
class Document
{
    /**
     * Identifiant unique du document
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['document:read', 'course:read', 'course:write'])]
    private ?int $id = null;

    /**
     * Titre du document
     * Exemple : "Chapitre 1 - Introduction"
     * Chaîne de caractères limitée à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['document:read', 'document:write', 'course:read', 'course:write'])]
    private ?string $title = null;

    /**
     * Chemin ou URL du document
     * Peut correspondre à :
     * - un chemin local sur le serveur
     * - une URL externe
     *
     * Limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['document:read', 'document:write', 'course:read', 'course:write'])]
    private ?string $path = null;

    /**
     * Cours auquel ce document appartient
     * Relation plusieurs-à-un :
     * Plusieurs documents peuvent être associés à un même cours
     *
     * Cette relation est obligatoire (nullable: false)
     */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:write'])]
    private ?Course $course = null;

    /**
     * Récupère l’identifiant unique du document
     *
     * @return ?int ID du document
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre du document
     *
     * @return ?string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre du document
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
     * Récupère le chemin ou l’URL du document
     *
     * @return ?string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Définit le chemin ou l’URL du document
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
     * Récupère le cours associé à ce document
     *
     * @return ?Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * Définit le cours associé à ce document
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
