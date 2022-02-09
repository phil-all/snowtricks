<?php

namespace App\Service\Entity;

use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\Common\Collections\Collection;

/**
 * Used to help trick media management
 */
class TrickMediaService
{
    /**
     * @var string|null
     */
    private ?string $thumbnailPath;

    /**
     * @var array|null
     */
    private ?array $imagesPathList;

    /**
     * @var array|null
     */
    private ?array $videosPathList;

    /**
     * @var Trick
     */
    private Trick $trick;

    /**
     * Constructor
     *
     * @param Trick $trick
     */
    public function __construct(Trick $trick)
    {
        $this->trick = $trick;
        $this->setGettersPath();
    }

    /**
     * Gets filererd Media collection by type
     *
     * @param string $type
     *
     * @return Collection
     */
    public function getFilteredMediaCollection(string $type): Collection
    {
        /** @var Collection $mediaCollection */
        $mediaCollection = $this->trick->getMedia();

        return $mediaCollection->filter(function (Media $media) use ($type) {
            return $media->getType()->getType() === $type;
        });
    }

    /**
     * Gets thumbnail path
     *
     * @return string|null
     */
    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    /**
     * Gets images path list
     *
     * @return array
     */
    public function getImagesPathList(): ?array
    {
        return $this->imagesPathList;
    }

    /**
     * Gets videos path list
     *
     * @return array
     */
    public function getVideosPathList(): ?array
    {
        return $this->videosPathList;
    }

    /**
     * Sets path getters properties
     *
     * @return void
     */
    private function setGettersPath(): void
    {
        /** @var array $medias */
        $medias = $this->trick->getMedia()->getValues();

        /** @var  Media $media */
        foreach ($medias as $key => $media) {
            if ($media->getType()->getType() === 'thumbnail') {
                $thumbnailPath = $media->getPath();
            }

            if ($media->getType()->getType() === 'image') {
                $imagesPath[] = $media->getPath();
            }
            if ($media->getType()->getType() === 'video') {
                $videosPath[] = $media->getPath();
            }
        }

        $this->thumbnailPath  = $thumbnailPath ?? null;
        $this->imagesPathList = $imagesPath ?? null;
        $this->videosPathList = $videosPath ?? null;
    }
}
