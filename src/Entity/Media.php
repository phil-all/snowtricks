<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, fetch="EAGER", inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="media")
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="media")
     */
    private $user;

    /**
     * @var UploadedFile|null
     */
    private ?UploadedFile $file = null;

    /**
     * @var string|null
     */
    private ?string $swapVideo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of file
     *
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @param  UploadedFile  $file
     *
     * @return  self
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of swapVideo
     *
     * @return string|null
     */
    public function getSwapVideo(): ?string
    {
        return $this->swapVideo;
    }

    /**
     * Set the value of swapVideo
     *
     * @param  string  $swapVideo
     *
     * @return  self
     */
    public function setSwapVideo(string $swapVideo): self
    {
        $this->swapVideo = $swapVideo;

        return $this;
    }
}
