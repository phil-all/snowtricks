<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use DateTimeImmutable;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;

/**
 * Trick service
 */
class TrickService
{
    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;

    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * TrickService constructor
     *
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository, TrickRepository $trickRepository)
    {
        $this->mediaRepository = $mediaRepository;
        $this->trickRepository = $trickRepository;
    }

    /**
     * Initialiaze new trick setting
     *
     * @param User $user
     *
     * @return Trick
     */
    public function setNew(User $user): Trick
    {
        /** @var DateTimeImmutable */
        $now = new DateTimeImmutable('now');

        return (new trick())
            ->setUser($user)
            ->setCreatedAt($now)
            ->setUpdateAt($now);
    }

    /**
     * Delete a trick, its medias and their files
     *
     * @param Trick $trick
     *
     * @return void
     */
    public function delete(Trick $trick): void
    {
        /** @var array $mediaList */
        $mediaList = $this->mediaRepository->findBy(['trick' => $trick]);

        /** @var Media $media */
        foreach ($mediaList as $media) {
            $this->mediaRepository->deleteMedia($media);
        }

        $this->trickRepository->delete($trick);
    }
}
