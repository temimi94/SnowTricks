<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('title')]
#[orm\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TricksRepository::class)]


class Tricks
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]



    private ?int $id = null;

    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'Votre titre doit comporter au moins 10 caractères',
        maxMessage: 'Votre titre ne peut pas contenir plus de 255 caractères',
    )]
    #[ORM\Column(length: 255)]
    private ?string $title ;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 20,
        minMessage: 'Votre message ne peut pas contenir moin de 20 caractères',
    )]
    private ?string $content = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Category::class)]
    private $category;


    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private ?string $video = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;



    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->category = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    

    #[ORM\PrePersist]
    public function setUpdatedAtvalue()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): Collection
    {
        return $this->category;
    }


    public function addCategory(Category $categories): static
    {
        if (!$this->category->contains($categories)) {
            $this->category[] = $categories;
        }

        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

}
