<?php

namespace App\Service\Entity;

use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @return ArrayCollection
     */
    public function getFilteredMediaCollection(string $type): ArrayCollection
    {
        /** @var ArrayCollection $mediaCollection */
        $mediaCollection =  $this->trick->getMedia()->filter(function (Media $media) use ($type) {
            return $media->getType()->getType() === $type;
        });

        return new ArrayCollection($mediaCollection->getValues());
    }
}
