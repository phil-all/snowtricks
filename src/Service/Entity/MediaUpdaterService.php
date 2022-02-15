<?php

namespace App\Service\Entity;

use App\Entity\Type;
use App\Entity\Media;
use App\Entity\Trick;
use App\Service\Uploader;
use App\Repository\TypeRepository;
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
     * @var TypeRepository
     */
    private TypeRepository $typeRepository;

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
        TypeRepository $typeRepository,
        MediaRepository $mediaRepository
    ) {
        $this->publicUploader  = $publicUploader;
        $this->typeRepository  = $typeRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Replace a trick thumbnail
     *
     * @param UploadedFile|null $uploadedFile
     * @param Media|null        $media
     *
     * @return self
     */
    public function replaceThumbnail(?UploadedFile $uploadedFile, ?Media $media): self
    {
        if (null !== $uploadedFile) {
            if (null === $media) {
                /** @var Type $type */
                $type = $this->typeRepository->findOneBy(['type' => 'thumbnail']);

                $media = (new Media())->setType($type);
            }

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
    public function replaceAdditionnalImages(ArrayCollection $images, Trick $trick): self
    {
        /** @var Media $image */
        foreach ($images as $image) {
            if (null !== $image->getFile()) {
                /** @var UploadedFile $uploadedMediaFile*/
                $uploadedMediaFile = $image->getFile();

                if (null !== $image->getId()) {
                    $this->picturesProcess($uploadedMediaFile, $image);
                } else {
                    $this->createAdditionnalImage($uploadedMediaFile, $trick);
                }
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
    public function replaceVideos(ArrayCollection $videos, Trick $trick): self
    {
        /** @var Media $video */
        foreach ($videos as $video) {
            if (null !== $video->getSwapVideo()) {
                /** @var string $newVideoUrl*/
                $newVideoUrl = $this->embedYoutubeLink($video->getSwapVideo());

                if (null !== $video->getId()) {
                    $this->videoProcess($newVideoUrl, $video);
                } else {
                    $this->createVideo($trick, $newVideoUrl);
                }
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
     * Create an additionnal trick image
     *
     * @param UploadedFile|null $uploadedFile
     * @param Trick             $trick
     *
     * @return void
     */
    private function createAdditionnalImage(?UploadedFile $uploadedFile, Trick $trick): void
    {
        if (null !== $uploadedFile) {
            $this->publicUploader->uploadImage($uploadedFile);

            /** @var Type $type */
            $type = $this->typeRepository->findOneBy(['type' => 'image']);

            $this->mediaRepository->createTrickMedia($type, $trick, $this->publicUploader->getNewFileName());
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

    public function createVideo(Trick $trick, string $url)
    {
        /** @var Type $type */
        $type = $this->typeRepository->findOneBy(['type' => 'video']);

        $this->mediaRepository->createTrickMedia($type, $trick, $url);
    }

    /**
     * Get an emebd youtube link from a given url
     *
     * @param string $url
     *
     * @return string
     */
    private function embedYoutubeLink(string $url): string
    {
        // clean url begining
        $firstFilter = preg_replace(
            '/(^http(?:s?):\/\/(?:(www\.)*youtu(?:be\.com\/watch\?v=|\.be\/)))/',
            '',
            $url
        );

        // clean url end
        $secondFilter = preg_replace(
            '/(&(.*))/',
            '',
            $firstFilter
        );

        return 'http://www.youtube.com/embed/' . $secondFilter;
    }
}
