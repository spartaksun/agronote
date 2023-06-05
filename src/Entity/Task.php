<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use VertigoLabs\DoctrineFullTextPostgres\ORM\Mapping\TsVector;


#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Index(columns: ["created_by_id", "due_date"], name: "idx__task_due_date")]
#[ORM\Index(columns: ["status"], name: "idx__task_status")]
#[ORM\Index(columns: ["created_by_id", "status"], name: "idx__task_created_status")]
#[ORM\Index(columns: ["due_date"], name: "idx__task_date_due")]
#[ORM\Index(columns: ["description_fts"], name: "idx__task_description_fts", options: ['gin'])]
class Task
{
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Attributes are used by Doctrine but TsVector uses annotations.
     * So keep them both %(
     */
    #[Constraints\Length(max: 5000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Constraints\Length(min: 1, max: 255)]
    /**
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private ?string $title = null;

    /**
     * @TsVector(name="description_fts", fields={"description", "title"})
     */
    private $descriptionFTS;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $dueDate = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function statuses(): array
    {
        return [
            'Pending' => Task::STATUS_PENDING,
            'In progress' => Task::STATUS_IN_PROGRESS,
            'Completed' => Task::STATUS_COMPLETED,
            'Cancelled' => Task::STATUS_CANCELLED
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
