<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Service\Entity\TrickMediaService;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 */
class Trick
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updateAt;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="trick", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="trick")
     */
    private $media;

    /**
     * @var Collection
     */
    private Collection $image;

    /**
     * @var Collection
     */
    private Collection $video;

    /**
     * @var TrickMediaService
     */
    private TrickMediaService $trickMediaService;

    public function __construct()
    {
        $this->media    = new ArrayCollection();
        $this->comments = new ArrayCollection();

        $this->setImage();
        $this->setVideo();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->media->contains($media)) {
            $this->media[] = $media;
            $media->setTrick($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->media->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getTrick() === $this) {
                $media->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * Get image value
     *
     * @return Collection
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    /**
     * Get video value
     *
     * @return Collection
     */
    public function getVideo(): Collection
    {
        return $this->video;
    }

    /**
     * Set the value of image
     */
    private function setImage(): void
    {
        $this->image =  $this->getOnceMediaService()->getFilteredMediaCollection('image');
    }

    /**
     * Set the value of video
     */
    private function setVideo(): void
    {
        $this->video =  $this->getOnceMediaService()->getFilteredMediaCollection('video');
    }


    /**
     * Gets thumbnail path
     *
     * @return string|null
     */
    public function getThumbnailPath(): ?string
    {
        return $this->getOnceMediaService()->getThumbnailPath();
    }

    /**
     * Gets images path list
     *
     * @return array|null
     */
    public function getImagesPathList(): ?array
    {
        return $this->getOnceMediaService()->getImagesPathList();
    }

    /**
     * Gets videos path list
     *
     * @return array|null
     */
    public function getVideosPathList(): ?array
    {
        return $this->getOnceMediaService()->getVideosPathList();
    }

    /**
     * Sets once TrickMediaService
     *
     * @return TrickMediaService
     */
    private function getOnceMediaService(): TrickMediaService
    {
        $this->trickMediaService = $this->trickMediaService ?? new TrickMediaService($this);

        return $this->trickMediaService;
    }
}
