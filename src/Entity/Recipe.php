<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Validator\BanWord;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[UniqueEntity('title')]
#[UniqueEntity('slug')]
class Recipe
{
    public const VALIDATION_GROUP_NEW = 'new_recipe';
    public const VALIDATION_GROUP_EDIT = 'edit_recipe';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Sequentially(constraints: [
        new Assert\NotBlank(groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT]),
        new Assert\Length(min: 4, max: 255, groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT]),
        new BanWord(groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT])
    ])]
    private string $title = '';

    #[ORM\Column(length: 255)]
    #[Assert\Sequentially(constraints: [
        new Assert\NotBlank(groups: [self::VALIDATION_GROUP_EDIT]),
        new Assert\Length(max: 255, groups: [self::VALIDATION_GROUP_NEW]),
        new Assert\Length(min: 4, max: 255, groups: [self::VALIDATION_GROUP_EDIT]),
        new Assert\Regex(pattern: "/^[a-z0-9-]+(?:-[a-z0-9-]+)*$/", message: "Ceci n'est pas un slug d'url valide", groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT])
    ])]
    private string $slug = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT])]
    private string $content = '';

    #[ORM\Column(nullable: true)]
    #[Assert\Sequentially(constraints: [
        new Assert\NotBlank(groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT]),
        new Assert\Positive(groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT]),
        // less 24h in minutes
        new Assert\LessThan(value: 1440, groups: [self::VALIDATION_GROUP_NEW, self::VALIDATION_GROUP_EDIT])
    ])]
    private ?int $duration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'recipes', cascade: ['persist'])]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
