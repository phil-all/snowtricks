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
     * @var Media
     */
    private Media $thumbnail;

    /**
     * @var Collection
     */
    private Collection $images;

    /**
     * @var Collection
     */
    private Collection $videos;

    /**
     * @var TrickMediaService
     */
    private TrickMediaService $trickMediaService;

    public function __construct()
    {
        $this->media    = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Get the value of thumbnail
     *
     * @return  Media|null
     */
    public function getThumbnail(): ?Media
    {
        return $this->getOnceMediaService()->getFilteredMediaCollection('thumbnail')->first();
    }

    /**
     * Set the value of thumbnail
     *
     * @param  Media  $thumbnail
     *
     * @return  self
     */
    public function setThumbnail(Media $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get the value of image
     *
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->getOnceMediaService()->getFilteredMediaCollection('image');
    }

    /**
     * Set the value of images
     *
     * @param Collection $collection
     *
     * @return self
     */
    public function setImages(Collection $collection): self
    {
        $this->images = $collection;

        return $this;
    }

    /**
     * Get the value of video
     *
     * @return Collection
     */
    public function getVideos(): Collection
    {
        return  $this->getOnceMediaService()->getFilteredMediaCollection('video');
    }

    /**
     * set the value of videos
     *
     * @param Collection $collection
     *
     * @return self
     */
    public function setVideos(Collection $collection): self
    {
        $this->images = $collection;

        return $this;
    }

    /**
     * Sets once TrickMediaService
     *
     * @return TrickMediaService
     */
    public function getOnceMediaService(): TrickMediaService
    {
        $this->trickMediaService = $this->trickMediaService ?? new TrickMediaService($this);

        return $this->trickMediaService;
    }
}
