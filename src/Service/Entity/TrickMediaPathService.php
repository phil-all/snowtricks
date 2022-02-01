<?php

namespace App\Service\Entity;

use App\Entity\Trick;

/**
 * TrickMediaPathService
 */
class TrickMediaPathService
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
     * Constructor
     *
     * @param Trick $trick
     */
    public function __construct(Trick $trick)
    {
        $this->setGettersProperties($trick);
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
     * Sets getters properties
     *
     * @param Trick $trick
     *
     * @return void
     */
    private function setGettersProperties(Trick $trick): void
    {
        $medias = $trick->getMedia()->getValues();

        foreach ($medias as $key => $entity) {
            if ($entity->getType()->getType() === 'thumbnail') {
                $thumbnail = $entity->getPath();
            }

            if ($entity->getType()->getType() === 'image') {
                $images[] = $entity->getPath();
            }
            if ($entity->getType()->getType() === 'video') {
                $videos[] = $entity->getPath();
            }
        }

        $this->thumbnailPath  = $thumbnail ?? null;
        $this->imagesPathList = $images ?? null;
        $this->videosPathList = $videos ?? null;
    }
}
