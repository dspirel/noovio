<?php

namespace App\Entity;

use App\Repository\TaskScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskScheduleRepository::class)]
class TaskSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'TaskSchedule')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $nextRunAt = null;

    #[ORM\Column]
    private ?\DateInterval $repeatEvery = null;

    #[ORM\Column(length: 255)]
    private ?string $targetPlatform = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookPage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagramPage = null;

    /**
     * @var Collection<int, TaskPost>
     */
    #[ORM\OneToMany(targetEntity: TaskPost::class, mappedBy: 'taskSchedule')]
    private Collection $taskPosts;

    public function __construct()
    {
        $this->taskPosts = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getNextRunAt(): ?\DateTimeImmutable
    {
        return $this->nextRunAt;
    }

    public function setNextRunAt(\DateTimeImmutable $nextRunAt): static
    {
        $this->nextRunAt = $nextRunAt;

        return $this;
    }

    public function getRepeatEvery(): ?\DateInterval
    {
        return $this->repeatEvery;
    }

    public function setRepeatEvery(\DateInterval $repeatEvery): static
    {
        $this->repeatEvery = $repeatEvery;

        return $this;
    }

    public function getTargetPlatform(): ?string
    {
        return $this->targetPlatform;
    }

    public function setTargetPlatform(string $targetPlatform): static
    {
        $this->targetPlatform = $targetPlatform;

        return $this;
    }

    public function getFacebookPage(): ?string
    {
        return $this->facebookPage;
    }

    public function setFacebookPage(?string $facebookPage): static
    {
        $this->facebookPage = $facebookPage;

        return $this;
    }

    public function getInstagramPage(): ?string
    {
        return $this->instagramPage;
    }

    public function setInstagramPage(?string $instagramPage): static
    {
        $this->instagramPage = $instagramPage;

        return $this;
    }

    /**
     * @return Collection<int, TaskPost>
     */
    public function getTaskPosts(): Collection
    {
        return $this->taskPosts;
    }

    public function addTaskPost(TaskPost $taskPost): static
    {
        if (!$this->taskPosts->contains($taskPost)) {
            $this->taskPosts->add($taskPost);
            $taskPost->setTaskSchedule($this);
        }

        return $this;
    }

    public function removeTaskPost(TaskPost $taskPost): static
    {
        if ($this->taskPosts->removeElement($taskPost)) {
            // set the owning side to null (unless already changed)
            if ($taskPost->getTaskSchedule() === $this) {
                $taskPost->setTaskSchedule(null);
            }
        }

        return $this;
    }
}
