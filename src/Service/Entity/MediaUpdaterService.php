<?php

namespace App\Service\Entity;

use App\Entity\Trick;
use App\Service\Uploader;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * MediaUpdaterService
 */
class MediaUpdaterService
{
    /**
     * @var Uploader
     */
    private Uploader $publicUploader;

    /**
     * @var MediaRepository
     */
    private MediaRepository $mediaRepository;

    /**
     * MediaUpdaterService constructor.
     *
     * @param Uploader        $publicUploader
     * @param MediaRepository $mediaRepository
     */
    public function __construct(
        Uploader $publicUploader,
        MediaRepository $mediaRepository
    ) {
        $this->publicUploader  = $publicUploader;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Processing trick thumbnail upload.
     *
     * @param UploadedFile|null $uploadedFile
     * @param Trick             $trick
     *
     * @return void
     */
    public function replaceThumbnailProcess(?UploadedFile $uploadedFile, Trick $trick): void
    {
        if (null !== $uploadedFile) {
            $this->publicUploader->uploadImage($uploadedFile);

            $this->mediaRepository->replaceThumbnail($trick, $this->publicUploader->getNewFileName());
        }
    }
}
