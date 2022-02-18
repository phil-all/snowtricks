<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Used to help trick media management
 */
class MediaAccessorService
{
    /**
     * @var User|Trick
     */
    private User|Trick $owner;

    /**
     * Constructor
     *
     * @param User|Trick $owner
     */
    public function __construct(User|Trick $owner)
    {
        $this->owner = $owner;
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
        $mediaCollection =  $this->owner->getMedia()->filter(function (Media $media) use ($type) {
            return $media->getType()->getType() === $type;
        });

        return new ArrayCollection($mediaCollection->getValues());
    }
}
