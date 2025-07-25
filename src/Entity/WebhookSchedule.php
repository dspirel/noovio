<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Cron\CronExpression;

#[ORM\Entity]
class WebhookSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $cronExpression = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $webhookUrl = null;

    #[ORM\Column(type: 'text')]
    #[Assert\Json(message: 'The JSON data must be valid.')]
    private ?string $jsonData = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $nextRunTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $user): self
    {
        $this->owner = $user;
        return $this;
    }

    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    public function setCronExpression(string $cronExpression): self
    {
        $this->cronExpression = $cronExpression;
        $this->calculateNextRunTime();
        return $this;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): self
    {
        $this->webhookUrl = $webhookUrl;
        return $this;
    }

    public function getJsonData(): ?string
    {
        return $this->jsonData;
    }

    public function setJsonData(string $jsonData): self
    {
        $this->jsonData = $jsonData;
        return $this;
    }

    public function getNextRunTime(): ?\DateTime
    {
        return $this->nextRunTime;
    }

    public function setNextRunTime(\DateTime $nextRunTime): self
    {
        $this->nextRunTime = $nextRunTime;
        return $this;
    }

    public function calculateNextRunTime(): void
    {
        $cron = new CronExpression($this->cronExpression);
        $this->nextRunTime = $cron->getNextRunDate(new \DateTime());
    }
}

// use App\Repository\WebhookScheduleRepository;
// use Doctrine\ORM\Mapping as ORM;
// use Cron\CronExpression;

// #[ORM\Entity(repositoryClass: WebhookScheduleRepository::class)]
// class WebhookSchedule
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\ManyToOne(inversedBy: 'webhookSchedules')]
//     #[ORM\JoinColumn(nullable: false)]
//     private ?User $owner = null;

//     #[ORM\Column(length: 255)]
//     private ?string $url = null;

//     #[ORM\Column(length: 10000)]
//     private ?string $payload = null;

//     #[ORM\Column(length: 255)]
//     private ?string $cronExpression = null;

//     #[ORM\Column]
//     private ?\DateTimeImmutable $nextRunAt = null;

//     #[ORM\Column]
//     private ?bool $enabled = null;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getOwner(): ?User
//     {
//         return $this->owner;
//     }

//     public function setOwner(?User $owner): static
//     {
//         $this->owner = $owner;

//         return $this;
//     }

//     public function getUrl(): ?string
//     {
//         return $this->url;
//     }

//     public function setUrl(string $url): static
//     {
//         $this->url = $url;

//         return $this;
//     }

//     public function getPayload(): ?string
//     {
//         return $this->payload;
//     }

//     public function setPayload(string $payload): static
//     {
//         $this->payload = $payload;

//         return $this;
//     }

//     public function getCronExpression(): ?string
//     {
//         return $this->cronExpression;
//     }

//     public function setCronExpression(string $cronExpression): static
//     {
//         $this->cronExpression = $cronExpression;

//         return $this;
//     }

//     public function getNextRunAt(): ?\DateTimeImmutable
//     {
//         return $this->nextRunAt;
//     }

//     public function setNextRunAt(\DateTimeImmutable $nextRunAt): static
//     {
//         $this->nextRunAt = $nextRunAt;

//         return $this;
//     }

//     public function isEnabled(): ?bool
//     {
//         return $this->enabled;
//     }

//     public function setEnabled(bool $enabled): static
//     {
//         $this->enabled = $enabled;

//         return $this;
//     }

//     public function calculateNextRunTime(): void
//     {
//         $cron = new CronExpression($this->cronExpression);
//         $this->nextRunAt = $cron->getNextRunDate();
//     }
// }
