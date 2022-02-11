<?php

namespace App\Service\Entity;

use App\Entity\Media;
use App\Entity\Trick;
use App\Service\Uploader;
use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
     * Replace a trick thumbnail
     *
     * @param UploadedFile|null $uploadedFile
     * @param Media             $media
     *
     * @return self
     */
    public function replaceThumbnail(?UploadedFile $uploadedFile, Media $media): self
    {
        if (null !== $uploadedFile) {
            $this->picturesProcess($uploadedFile, $media);
        }

        return $this;
    }

    /**
     * Replace trick additionnal images
     *
     * @param ArrayCollection $images
     *
     * @return self
     */
    public function replaceAdditionnalImages(ArrayCollection $images): self
    {
        foreach ($images as $image) {
            if (null !== $image->getFile()) {
                /** @var UploadedFile $uploadedMediaFile*/
                $uploadedMediaFile = $image->getFile();

                $this->picturesProcess($uploadedMediaFile, $image);
            }
        }

        return $this;
    }

    /**
     * Replace trick videos
     *
     * @param ArrayCollection $videos
     *
     * @return self
     */
    public function replaceVideos(ArrayCollection $videos): self
    {
        foreach ($videos as $video) {
            if (null !== $video->getSwapVideo()) {
                /** @var string $newVideoUrl*/
                $newVideoUrl = $video->getSwapVideo();

                $this->videoProcess($newVideoUrl, $video);
            }
        }

        return $this;
    }

    /**
     * Process pictures update
     *
     * @param UploadedFile|null $uploadedFile
     * @param Media             $media
     *
     * @return void
     */
    private function picturesProcess(?UploadedFile $uploadedFile, Media $media): void
    {
        if (null !== $uploadedFile) {
            $this->publicUploader->uploadImage($uploadedFile);

            $this->mediaRepository->replaceTrickMedia($media, $this->publicUploader->getNewFileName());
        }
    }

    /**
     * Process videos update
     *
     * @param string|null $newVideoUrl
     * @param Media       $media
     *
     * @return void
     */
    private function videoProcess(?string $newVideoUrl, Media $media): void
    {
        if (null !== $newVideoUrl) {
            $this->mediaRepository->replaceTrickMedia($media, $newVideoUrl);
        }
    }
}
