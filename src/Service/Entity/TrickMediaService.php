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
}
