<?php

namespace App\Entity;

use App\Domain\Service;
use App\Repository\UserContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserContactRepository::class)]
class UserContact
{
    public const SERVICES = ['digital@email.com','dsi@email.com','communication@email.com','rh@email.com'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[Assert\Regex(pattern: '/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/')]
    private string $email = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $message = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::SERVICES, message: 'Il faut choisir un service existant')]
    private ?string $Service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->Service;
    }

    public function setService(?string $Service): static
    {
        $this->Service = $Service;

        return $this;
    }
}
