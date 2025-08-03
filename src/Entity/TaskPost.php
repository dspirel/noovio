<?php

namespace App\Entity;

use App\Repository\TaskPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskPostRepository::class)]
class TaskPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taskPosts')]
    private ?TaskSchedule $taskSchedule = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskSchedule(): ?TaskSchedule
    {
        return $this->taskSchedule;
    }

    public function setTaskSchedule(?TaskSchedule $taskSchedule): static
    {
        $this->taskSchedule = $taskSchedule;

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
}
