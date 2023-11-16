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

    #[ORM\Column(length: 255)]
    private ?string $name = null;

//    #[ORM\ManyToMany(targetEntity: Quack::class, inversedBy: 'tags')]
    #[ORM\ManyToMany(targetEntity: Quack::class, mappedBy: 'tags')]

    private Collection $quack_id;

    public function __construct()
    {
        $this->quack_id = new ArrayCollection();
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

    /**
     * @return Collection<int, Quack>
     */
    public function getQuackId(): Collection
    {
        return $this->quack_id;
    }

    public function addQuackId(Quack $quackId): static
    {
        if (!$this->quack_id->contains($quackId)) {
            $this->quack_id->add($quackId);
        }

        return $this;
    }

    public function removeQuackId(Quack $quackId): static
    {
        $this->quack_id->removeElement($quackId);

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
}
