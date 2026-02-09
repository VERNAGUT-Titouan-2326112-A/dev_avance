<?php

namespace App\Entity;

use App\Repository\CourseRepository;
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
 * Entité Course - Représente un cours dans le système
 *
 * Cette entité gère la structure d'un cours avec ses informations générales
 * (titre, description, matière, niveau) ainsi que ses ressources associées
 * (vidéos, documents, Quiz). Elle expose une API REST complète pour les opérations CRUD.
 *
 * Groupes de sérialisation:
 * - course:read: Utilisé pour la sérialisation (lecture) des cours
 * - course:write: Utilisé pour la désérialisation (écriture) des cours
 */
#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère la liste de tous les cours'),
        new Get(description: 'Récupère les détails d\'un cours (avec vidéos et documents)'),
        new Post(description: 'Crée un nouveau cours'),
        new Put(description: 'Met à jour un cours existant'),
        new Delete(description: 'Supprime un cours'),
    ],
    normalizationContext: ['groups' => ['course:read']],  
    denormalizationContext: ['groups' => ['course:write']]  
)]
class Course
{
    /**
     * Identifiant unique du cours
     * Clé primaire auto-générée par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['course:read'])]
    private ?int $id = null;

    /**
     * Titre/nom du cours
     * Exemple: "Mathématiques Avancées", "Histoire de France"
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255)]
    #[Groups(['course:read', 'course:write'])]
    private ?string $title = null;

    /**
     * Description détaillée du cours
     * Peut contenir plusieurs paragraphes et explications sur le contenu du cours
     * Texte long sans limite de caractères (type TEXT en base de données)
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['course:read', 'course:write'])]
    private ?string $description = null;

    /**
     * Matière/domaine du cours
     * Exemple: "Mathématiques", "Sciences", "Langues"
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['course:read', 'course:write'])] 
    private ?string $subject = null;

    /**
     * Niveau de difficulté ou année du cours
     * Exemple: "Débutant", "Intermédiaire", "Avancé" ou "1ère année", "2nde année"
     * Chaîne de caractères limité à 255 caractères
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['course:read', 'course:write'])] 
    private ?string $level = null;

    /**
     * Date de création du cours
     * Enregistrée automatiquement à la création du cours
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['course:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Date de dernière modification du cours
     * Mise à jour automatiquement à chaque modification
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['course:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Collection des Quiz associés à ce cours
     * Relation "un-à-plusieurs" : un cours peut avoir plusieurs Quiz
     * La suppression du cours ne supprime pas les Quiz (cascade:persist uniquement)
     *
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'course', orphanRemoval: true, cascade: ['persist'])]
    #[Groups(['course:read', 'course:write'])] 
    private Collection $qcms;


    /**
     * Collection des vidéos pédagogiques du cours
     * Relation "un-à-plusieurs" : un cours peut avoir plusieurs vidéos
     * Orphan removal: les vidéos orphelines sont supprimées
     *
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'course', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['course:read', 'course:write'])]
    private Collection $videos;

    /**
     * Collection des documents du cours (PDF, slides, etc.)
     * Relation "un-à-plusieurs" : un cours peut avoir plusieurs documents
     * Orphan removal: les documents orphelines sont supprimées
     *
     * @var Collection<int, Document>
     */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'course', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['course:read', 'course:write'])]
    private Collection $documents;

    /**
     * Professeur responsable de ce cours
     * Relation "plusieurs-à-un" : plusieurs cours peuvent être gérés par un seul professeur
     *
     * @var ?Teacher
     */
    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['course:read', 'course:write'])] 
    private ?Teacher $teacher = null;

    /**
     * Constructeur de l'entité Course
     * Initialise les collections vides pour les Quiz
     */
    public function __construct()
    {
        $this->qcms = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Récupère l'identifiant unique du cours
     *
     * @return ?int L'ID du cours, null si non encore persisté
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre du cours
     *
     * @return ?string Le titre du cours
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre du cours
     *
     * @param string $title Le titre à assigner au cours
     * @return static Instance courante pour permettre l'appel en chaîne (fluent interface)
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Récupère la description du cours
     *
     * @return ?string La description du cours
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description du cours
     *
     * @param ?string $description La description à assigner au cours
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Récupère la matière du cours
     *
     * @return ?string La matière du cours
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Définit la matière du cours
     *
     * @param ?string $subject La matière à assigner au cours
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Récupère le niveau du cours
     *
     * @return ?string Le niveau du cours
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * Définit le niveau du cours
     *
     * @param ?string $level Le niveau à assigner au cours
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setLevel(?string $level): static
    {
        $this->level = $level;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Récupère la date de création du cours
     *
     * @return ?\DateTimeImmutable La date de création
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Récupère la date de dernière modification du cours
     *
     * @return ?\DateTimeImmutable La date de dernière modification
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Récupère tous les Quiz associés à ce cours
     *
     * @return Collection<int, Quiz> Collection d'objets Quiz
     */
    public function getQcms(): Collection
    {
        return $this->qcms;
    }

    /**
     * Ajoute un Quiz au cours
     * Évite les doublons et maintient la relation bidirectionnelle
     *
     * @param Quiz $qcm Le Quiz à ajouter
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function addQcm(Quiz $qcm): static
    {
        if (!$this->qcms->contains($qcm)) {
            $this->qcms->add($qcm);
            $qcm->setCourse($this);
        }

        return $this;
    }

    /**
     * Retire un Quiz du cours
     * Maintient la cohérence de la relation bidirectionnelle
     *
     * @param Quiz $qcm Le Quiz à retirer
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function removeQcm(Quiz $qcm): static
    {
        if ($this->qcms->removeElement($qcm)) {
            if ($qcm->getCourse() === $this) {
                $qcm->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * Récupère le professeur responsable du cours
     *
     * @return ?Teacher Le professeur, null si non assigné
     */
    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    /**
     * Définit le professeur responsable du cours
     *
     * @param ?Teacher $teacher Le professeur à assigner, null pour dissocier
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Récupère toutes les vidéos du cours
     *
     * @return Collection<int, Video> Collection des vidéos
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    /**
     * Ajoute une vidéo au cours
     *
     * @param Video $video La vidéo à ajouter
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setCourse($this);
        }
        return $this;
    }

    /**
     * Retire une vidéo du cours
     *
     * @param Video $video La vidéo à retirer
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            if ($video->getCourse() === $this) {
                $video->setCourse(null);
            }
        }
        return $this;
    }

    /**
     * Récupère tous les documents du cours
     *
     * @return Collection<int, Document> Collection des documents
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * Ajoute un document au cours
     *
     * @param Document $document Le document à ajouter
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setCourse($this);
        }
        return $this;
    }

    /**
     * Retire un document du cours
     *// 👈 AJOUTÉ
     * @param Document $document Le document à retirer
     * @return static Instance courante pour permettre l'appel en chaîne
     */
    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getCourse() === $this) {
                $document->setCourse(null);
            }
        }
        return $this;
    }
}