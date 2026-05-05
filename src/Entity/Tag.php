<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'tags')]
    private Collection $postTag;

    public function __construct()
    {
        $this->postTag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPostTag(): Collection
    {
        return $this->postTag;
    }

    public function addPostTag(Post $postTag): static
    {
        if (!$this->postTag->contains($postTag)) {
            $this->postTag->add($postTag);
        }

        return $this;
    }

    public function removePostTag(Post $postTag): static
    {
        $this->postTag->removeElement($postTag);

        return $this;
    }
}
