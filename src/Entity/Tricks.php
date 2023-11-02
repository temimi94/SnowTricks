<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private ?string $title = null;
    

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 20,
        minMessage: 'Votre message ne peut pas contenir moin de 20 caractères',
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $groupeTrick = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private ?string $video = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getGroupeTrick(): ?string
    {
        return $this->groupeTrick;
    }

    public function setGroupeTrick(string $groupeTrick): static
    {
        $this->groupeTrick = $groupeTrick;

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
}
