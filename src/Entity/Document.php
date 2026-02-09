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

    #[ORM\Entity(repositoryClass: DocumentRepository::class)]
    #[ApiResource(
        operations: [
            new GetCollection(),
            new Get(),
            new Post(),
            new Put(),
            new Delete(),
        ],
        normalizationContext: ['groups' => ['document:read']],
        denormalizationContext: ['groups' => ['document:write']]
    )]
    class Document
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        #[Groups(['document:read', 'course:read', 'course:write'])]
        private ?int $id = null;

        #[ORM\Column(length: 255)]
        #[Groups(['document:read', 'document:write', 'course:read', 'course:write'])]
        private ?string $title = null;

        #[ORM\Column(length: 255)]
        #[Groups(['document:read', 'document:write', 'course:read', 'course:write'])]
        private ?string $path = null;

        #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'documents')]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['document:write'])]
        private ?Course $course = null;

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getTitle(): ?string
        {
            return $this->title;
        }

        public function setTitle(string $title): static
        {
            $this->title = $title;

            return $this;
        }

        public function getPath(): ?string
        {
            return $this->path;
        }

        public function setPath(string $path): static
        {
            $this->path = $path;

            return $this;
        }

        public function getCourse(): ?Course
        {
            return $this->course;
        }

        public function setCourse(?Course $course): static
        {
            $this->course = $course;

            return $this;
        }
    }