<?php

// src/Entity/Unicorn.php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post, Patch, Put, Delete};
use App\Repository\UnicornRepository;
use App\State\PurchaseUnicornProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnicornRepository::class)]

#[ApiResource(
    description: "API endpoint for Unicorns",
    operations: [
        new Get(
            openapiContext: [
                "summary" => "Retrieves a Unicorn at the farm.",
                "description" => "Retrieves a Unicorn at the farm."
            ]
        ),
        new GetCollection(
            openapiContext: [
                "summary" => "Retrieves the collection of Unicorns at the farm.",
                "description" => "Retrieves the collection of Unicorns at the farm."
            ]
        ),
        new Post(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Patch(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Put(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Delete(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        )
    ]
)]
#[ApiResource(
    uriTemplate: "unicorns/{id}/purchase",
    description: "API endpoint for unicorn purchases",
    operations: [
        new Get(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new GetCollection(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Post(
            processor: PurchaseUnicornProcessor::class,
            status: 200,
            openapiContext: [
                "summary" => "Purchases a unicorn",
                "description" => "Allows purchasing a unicorn and triggers an email containing related posts. The posts are then deleted.",
                "requestBody" => [
                    "content" => [
                        "application/ld+json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "email" => [
                                        "type" => "string",
                                        "format" => "email",
                                        "description" => "Email address where related posts will be sent."
                                    ],
                                ],
                                "required" => ["email"],
                            ],
                        ],
                    ],
                    "required" => true,
                ],
                "responses" =>  [
                    200 => [
                        "description" => "Unicorn purchased",
                        "content" => [
                            "application/ld+json" => [
                                "schema" => [
                                    "type" => "object",
                                    "properties" => [
                                        "@context" => [
                                            "type" => "string",
                                        ],
                                        "@id" => [
                                            "type" => "string",
                                        ],
                                        "@type" => [
                                            "type" => "string",
                                        ],
                                        "id" => [
                                            "type" => "integer",
                                        ],
                                        "name" => [
                                            "type" => "string",
                                        ],
                                        "messages" => [
                                            "type" => "array",
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ],
                    400 => [
                        "description" => "Invalid input",
                    ],
                    404 => [
                        "description" => "Unicorn not found",
                    ],
                    409 => [
                        "description" => "Unicorn already purchased",
                    ],
                    500 => [
                        "description" => "Purchase failed",
                    ],
                ]
            ]
        ),
        new Patch(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Put(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        ),
        new Delete(
            description: "Not implemented",
            controller: NotFoundAction::class,
            read: false,
            output: false,
            openapi: false
        )
    ]
)]
#[ORM\HasLifecycleCallbacks]
class Unicorn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isPurchased = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "unicorn", orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->isPurchased = false;
        $this->messages = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isPurchased(): ?bool
    {
        return $this->isPurchased;
    }

    public function setPurchased(bool $isPurchased): static
    {
        $this->isPurchased = $isPurchased;

        return $this;
    }

    #[ORM\PrePersist]
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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUnicorn($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUnicorn() === $this) {
                $message->setUnicorn(null);
            }
        }

        return $this;
    }
}
