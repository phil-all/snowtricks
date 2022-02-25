<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use DateTimeImmutable;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBagInterface;

    /**
     * TrickService constructor
     *
     * @param MediaRepository   $mediaRepository
     * @param TrickRepository   $trickRepository
     * @param FlashBagInterface $flashBagInterface
     */
    public function __construct(
        MediaRepository $mediaRepository,
        TrickRepository $trickRepository,
        FlashBagInterface $flashBagInterface
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->trickRepository = $trickRepository;
        $this->flashBagInterface = $flashBagInterface;
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
     * Update a trick
     *
     * @param Trick $trick
     *
     * @return void
     */
    public function update(Trick $trick): void
    {
        $slugger = new AsciiSlugger();

        $trick->setSlug($slugger->slug($trick->getTitle(), '-'))
            ->setUpdateAt(new DateTimeImmutable('now'));

        $subject =  (null === $trick->getId()) ? 'création' : 'mise à jour';

        $this->setFlashMessage($trick, $subject);

        $this->trickRepository->update($trick);
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

        $this->setFlashMessage($trick, 'suppression');

        $this->trickRepository->delete($trick);
    }

    /**
     * Set flash message related to trick process
     *
     * @param Trick  $trick
     * @param string $subject could be 'création', 'mise à jour' or 'suppression'
     *
     * @return void
     */
    private function setFlashMessage(Trick $trick, string $subject): void
    {
        /** @var ?string $title */
        $title = strtoupper($trick->getTitle());

        $this
            ->flashBagInterface
            ->add(
                'success',
                'La ' . $subject . ' du trick ' . $title . ' a été correctement effectuée.'
            );
    }
}
