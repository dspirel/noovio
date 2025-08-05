<?php

namespace App\Entity;

use App\Repository\TaskPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskPostRepository::class)]
class TaskPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;

    #[ORM\Column]
    private ?bool $posted = null;

    #[ORM\Column(length: 2500, nullable: true)]
    private ?string $postText = null;

    #[ORM\Column(nullable: true)]
    private ?array $mediaUrls = null;

    #[ORM\ManyToOne(inversedBy: 'taskPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, TaskSchedule>
     */
    #[ORM\ManyToMany(targetEntity: TaskSchedule::class, inversedBy: 'taskPosts')]
    private Collection $taskSchedules;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->taskSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isPosted(): ?bool
    {
        return $this->posted;
    }

    public function setPosted(bool $posted): static
    {
        $this->posted = $posted;

        return $this;
    }

    public function getPostText(): ?string
    {
        return $this->postText;
    }

    public function setPostText(?string $postText): static
    {
        $this->postText = $postText;

        return $this;
    }

    public function getMediaUrls(): ?array
    {
        return $this->mediaUrls;
    }

    public function setMediaUrls(?array $mediaUrls): static
    {
        $this->mediaUrls = $mediaUrls;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, TaskSchedule>
     */
    public function getTaskSchedules(): Collection
    {
        return $this->taskSchedules;
    }

    public function addTaskSchedule(TaskSchedule $taskSchedule): static
    {
        if (!$this->taskSchedules->contains($taskSchedule)) {
            $this->taskSchedules->add($taskSchedule);
            $taskSchedule->addTaskPost($this);
        }

        return $this;
    }

    public function removeTaskSchedule(TaskSchedule $taskSchedule): static
    {
        if ($this->taskSchedules->removeElement($taskSchedule)) {
            $taskSchedule->removeTaskPost($this);
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        //TODO: ADD DEFAULT TIMEZONE???
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
