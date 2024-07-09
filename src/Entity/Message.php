<?php

// src/Entity/Message.php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post, Patch, Put, Delete};
use App\Repository\MessageRepository;
use App\State\{CreatePostProcessor, UpdatePostProcessor};
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    description: "API endpoint for Posts",
    shortName: "Post",
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: CreatePostProcessor::class,
            denormalizationContext: [
                "groups" => ["message:write"],
                "allow_extra_attributes" => false
            ]
        ),
        new Put(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Patch(
            processor: UpdatePostProcessor::class,
            denormalizationContext: [
                "groups" => ["message:patch"],
                "allow_extra_attributes" => false
            ]
        ),
        new Delete(),
    ],
)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["message:write"])]
    #[Assert\NotBlank]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["message:write", "message:patch"])]
    #[Assert\NotBlank]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]

    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]

    private ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: "Messages")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["message:write"])]
    #[Assert\NotBlank]
    private ?Unicorn $unicorn = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUnicorn(): ?Unicorn
    {
        return $this->unicorn;
    }

    public function setUnicorn(?Unicorn $unicorn): static
    {
        $this->unicorn = $unicorn;

        return $this;
    }

    public function getCreatedAtValue(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    private function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUpdatedAtValue(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    private function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
