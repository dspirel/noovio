<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    #[ORM\Column(length: 180)]
    private ?string $facebookIdentifier = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, TaskSchedule>
     */
    #[ORM\OneToMany(targetEntity: TaskSchedule::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $TaskSchedule;

    /**
     * @var Collection<int, TaskPost>
     */
    #[ORM\OneToMany(targetEntity: TaskPost::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $taskPosts;


    public function __construct()
    {
        $this->TaskSchedule = new ArrayCollection();
        $this->taskPosts = new ArrayCollection();
    }

    public function getFacebookIdentifier(): ?string
    {
        return $this->facebookIdentifier;
    }

    public function setFacebookIdentifier(string $facebookIdentifier): static
    {
        $this->facebookIdentifier = $facebookIdentifier;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @return Collection<int, TaskSchedule>
     */
    public function getTaskSchedule(): Collection
    {
        return $this->TaskSchedule;
    }

    public function addTaskSchedule(TaskSchedule $taskSchedule): static
    {
        if (!$this->TaskSchedule->contains($taskSchedule)) {
            $this->TaskSchedule->add($taskSchedule);
            $taskSchedule->setOwner($this);
        }

        return $this;
    }

    public function removeTaskSchedule(TaskSchedule $taskSchedule): static
    {
        if ($this->TaskSchedule->removeElement($taskSchedule)) {
            // set the owning side to null (unless already changed)
            if ($taskSchedule->getOwner() === $this) {
                $taskSchedule->setOwner(null);
            }
        }

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
            $taskPost->setOwner($this);
        }

        return $this;
    }

    public function removeTaskPost(TaskPost $taskPost): static
    {
        if ($this->taskPosts->removeElement($taskPost)) {
            // set the owning side to null (unless already changed)
            if ($taskPost->getOwner() === $this) {
                $taskPost->setOwner(null);
            }
        }

        return $this;
    }

}
