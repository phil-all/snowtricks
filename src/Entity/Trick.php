<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\Collection;
use App\Service\Entity\MediaAccessorService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 *
 * @UniqueEntity(fields="title", message="Un trick du même nom existe déjà.")
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
     * @ORM\Column(type="string", length=255, unique=true)
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
     * @var MediaAccessorService
     */
    private MediaAccessorService $mediaAccessorService;

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

    /**
     * Get the value of thumbnail
     *
     * @return  Media|null
     */
    public function getThumbnail(): ?Media
    {
        /** @var Media|false */
        $thumbnail =  $this->getOnceMediaService()->getFilteredMediaCollection('thumbnail')->first();

        return (!$thumbnail) ? null : $thumbnail;
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
        $this->videos = $collection;

        return $this;
    }

    /**
     * Sets once MediaAccessorService
     *
     * @return MediaAccessorService
     */
    public function getOnceMediaService(): MediaAccessorService
    {
        $this->mediaAccessorService = $this->mediaAccessorService ?? new MediaAccessorService($this);

        return $this->mediaAccessorService;
    }
}
