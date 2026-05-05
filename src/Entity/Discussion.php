<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'discussions')]
    private Collection $discuss;

    /**
     * @var Collection<int, message>
     */
    #[ORM\OneToMany(targetEntity: message::class, mappedBy: 'discussion', orphanRemoval: true)]
    private Collection $include;

    public function __construct()
    {
        $this->discuss = new ArrayCollection();
        $this->include = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getDiscuss(): Collection
    {
        return $this->discuss;
    }

    public function addDiscuss(user $discuss): static
    {
        if (!$this->discuss->contains($discuss)) {
            $this->discuss->add($discuss);
        }

        return $this;
    }

    public function removeDiscuss(user $discuss): static
    {
        $this->discuss->removeElement($discuss);

        return $this;
    }

    /**
     * @return Collection<int, message>
     */
    public function getInclude(): Collection
    {
        return $this->include;
    }

    public function addInclude(message $include): static
    {
        if (!$this->include->contains($include)) {
            $this->include->add($include);
            $include->setDiscussion($this);
        }

        return $this;
    }

    public function removeInclude(message $include): static
    {
        if ($this->include->removeElement($include)) {
            // set the owning side to null (unless already changed)
            if ($include->getDiscussion() === $this) {
                $include->setDiscussion(null);
            }
        }

        return $this;
    }
}
