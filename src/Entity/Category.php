<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Validator\BanWord;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Sequentially(constraints: [
        new Assert\NotBlank,
        new Assert\Length(min: 2, max: 255),
        new BanWord
    ])]
    private string $name = '';

    #[ORM\Column(length: 255)]
    #[Assert\Sequentially(constraints: [
//        new Assert\NotBlank,
//        new Assert\Length(min: 2, max: 255),
        new Assert\Regex(pattern: "/^[a-z0-9-]+(?:-[a-z0-9-]+)*$/", message: "Ceci n'est pas un slug d'url valide")
    ])]
    private string $slug = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
}
