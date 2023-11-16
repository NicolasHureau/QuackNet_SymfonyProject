<?php

namespace App\Entity;

use App\Repository\QuackRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\SecurityBundle\Security;


#[ORM\Entity(repositoryClass: QuackRepository::class)]
class Quack
{

    public function __construct()
    {
        $this->created_at = new Datetime('now');
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column]
    private ?int $author_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

//    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'quack_id')]
    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="quack_id")
     * @ORM\JoinTable(name="tag_quack")
     */
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments')]
    private ?self $quack = null;

    #[ORM\OneToMany(mappedBy: 'quack', targetEntity: self::class)]
    private Collection $comments;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $author_id): static
    {
        $this->author_id = $author_id;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags ?? new ArrayCollection();
    }

    public function addTags(Collection $tags): self
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addQuackId($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeQuackId($this);
        }

        return $this;
    }

    /**
     * Set a single tag to the Quack.
     *
     * @param Tag $tag
     */
    public function setTag(Tag $tag): void
    {
        if (!$this->tags) {
            $this->tags = new ArrayCollection();
        }

        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addQuackId($this); // Make sure to update the inverse side
        }
    }

    public function getQuack(): ?self
    {
        return $this->quack;
    }

    public function setQuack(?self $quack): static
    {
        $this->quack = $quack;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setQuack($this);
        }

        return $this;
    }

    public function removeComment(self $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getQuack() === $this) {
                $comment->setQuack(null);
            }
        }

        return $this;
    }

}
