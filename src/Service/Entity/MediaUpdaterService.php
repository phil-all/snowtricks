<?php

namespace App\Service\Entity;

use App\Entity\Type;
use App\Entity\Media;
use App\Entity\Trick;
use App\Service\Uploader;
use App\Repository\TypeRepository;
use App\Repository\MediaRepository;
use Symfony\Component\Form\FormInterface;
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
     * @var Trick
     */
    private Trick $trick;

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
     * Update trick Medias from form datas
     *
     * @param Trick         $trick
     * @param FormInterface $form
     *
     * @return void
     */
    public function updateMedias(
        Trick $trick,
        FormInterface $form
    ): void {

        /** @var UploadedFile $formThumbnail */
        $formThumbnail = $form['thumbnail']->getData();

        /** @var ArrayCollection $formImages */
        $formImages = $form['images']->getData();

        /** @var ArrayCollection $formVideos */
        $formVideos = $form['videos']->getData();

        $this
            ->setTrick($trick)
            ->setThumbnail($formThumbnail)
            ->deleteImagesAndVideos($formImages, $formVideos)
            ->setAdditionnalImages($formImages)
            ->setVideos($formVideos);
    }

    /**
     * Set a trick thumbnail
     *
     * @param UploadedFile|null $uploadedFile
     *
     * @return self
     */
    private function setThumbnail(?UploadedFile $uploadedFile): self
    {
        if (null !== $uploadedFile) {
            /** @var Media $thumbnail */
            $thumbnail = $this->trick->getThumbnail();

            $this->picturesProcess($uploadedFile, $thumbnail);
        }

        return $this;
    }

    /**
     * Delete images and videos
     *
     * @param ArrayCollection $formImages
     * @param ArrayCollection $formVideos
     *
     * @return self
     */
    private function deleteImagesAndVideos(ArrayCollection $formImages, ArrayCollection $formVideos): self
    {
        /** @var array $trickImagesAndVideos */
        $trickImagesAndVideos = array_merge(
            $this->trick->getImages()->toArray(),
            $this->trick->getVideos()->toArray()
        );

        /** @var array $formImagesAndVideos */
        $formImagesAndVideos = array_merge(
            $formImages->toArray(),
            $formVideos->toArray()
        );

        /** @var array $mediasToDelete */
        $mediasToDelete = array_diff(
            $trickImagesAndVideos,
            $formImagesAndVideos
        );

        /** @var Media $media */
        foreach ($mediasToDelete as $media) {
            $this->mediaRepository->deleteMedia($media);
        }

        return $this;
    }

    /**
     * Set trick additionnal images
     *
     * @param ArrayCollection $formImages
     *
     * @return self
     */
    private function setAdditionnalImages(ArrayCollection $formImages): self
    {
        /** @var Media $media */
        foreach ($formImages as $media) {
            if (null !== $media->getFile()) {
                /** @var UploadedFile $uploadedMediaFile*/
                $uploadedMediaFile = $media->getFile();

                if (null !== $media->getId()) {
                    $this->picturesProcess($uploadedMediaFile, $media);
                } else {
                    $this->createAdditionnalImage($uploadedMediaFile);
                }
            }
        }

        return $this;
    }

    /**
     * Set trick videos
     *
     * @param ArrayCollection $formVideos
     *
     * @return self
     */
    private function setVideos(ArrayCollection $formVideos): self
    {
        /** @var Media $media */
        foreach ($formVideos as $media) {
            if (null !== $media->getSwapVideo()) {
                /** @var string $newVideoUrl*/
                $newVideoUrl = $this->embedYoutubeLink($media->getSwapVideo());

                if (null !== $media->getId()) {
                    $this->videoProcess($newVideoUrl, $media);
                } else {
                    $this->createVideo($newVideoUrl);
                }
            }
        }

        return $this;
    }

    /**
     * Process pictures update
     *
     * @param UploadedFile|null $uploadedFile
     * @param Media|null        $media
     *
     * @return void
     */
    private function picturesProcess(?UploadedFile $uploadedFile, ?Media $media): void
    {
        if (null !== $uploadedFile) {
            $this->publicUploader->uploadImage($uploadedFile);

            if (null === $media) {
                /** @var Type $type */
                $type = $this->typeRepository->findOneBy(['type' => 'thumbnail']);

                /** @var string $path */
                $path = $this->publicUploader->getNewFileName();

                $this->mediaRepository->createTrickMedia($type, $this->trick, $path);
            } else {
                $this->mediaRepository->replaceTrickMedia($media, $this->publicUploader->getNewFileName());
            }
        }
    }

    /**
     * Create an additionnal trick image
     *
     * @param UploadedFile|null $uploadedFile
     *
     * @return void
     */
    private function createAdditionnalImage(?UploadedFile $uploadedFile): void
    {
        if (null !== $uploadedFile) {
            $this->publicUploader->uploadImage($uploadedFile);

            /** @var Type $type */
            $type = $this->typeRepository->findOneBy(['type' => 'image']);

            /** @var string $path */
            $path = $this->publicUploader->getNewFileName();

            $this->mediaRepository->createTrickMedia($type, $this->trick, $path);
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

    /**
     * Create video
     *
     * @param string $url
     *
     * @return void
     */
    private function createVideo(string $url)
    {
        /** @var Type $type */
        $type = $this->typeRepository->findOneBy(['type' => 'video']);

        $this->mediaRepository->createTrickMedia($type, $this->trick, $url);
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

        return 'https://www.youtube.com/embed/' . $secondFilter;
    }

    /**
     * Set trick value
     *
     * @param Trick $trick
     *
     * @return self
     */
    private function setTrick(Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
