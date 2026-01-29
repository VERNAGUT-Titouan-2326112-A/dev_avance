<?php
namespace App\Entity;

use App\Repository\DynamicqcmRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DynamicqcmRepository::class)]
class Dynamicqcm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $theme;

    #[ORM\Column]
    private int $nbrQuestion;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function getNbrQuestion(): int
    {
        return $this->nbrQuestion;
    }

    public function setNbrQuestion(int $nbrQuestion): static
    {
        $this->nbrQuestion = $nbrQuestion;
        return $this;
    }
}
