<?php

namespace App\Service\Entity;

/**
 * TrickService
 */
class TrickService
{
    /**
     * Find thumbnail path in a media collection
     *
     * @param mixed  $media could be PersistentCollection or ArrayCollection
     * @param string $type  could be thumbnail, image or video
     *
     * @return mixed        could be null, string or array
     */
    public function findMediaPath(mixed $media, string $type): mixed
    {
        $medias = $media->toArray();

        if ($type === 'thumbnail') {
            return $this->findThunmbnail($medias);
        }

        return $this->findImagesOrVideos($medias, $type);
    }

    /**
     * Find thunmbnail
     *
     * @param array $medias
     */
    private function findThunmbnail(array $medias)
    {
        foreach ($medias as $key => $value) {
            if ($value->getType()->getId() === 2) {
                return $value->getFile();
            }
        }
    }

    /**
     * Find images or videos
     *
     * @param array $medias
     *
     * @return array|null
     */
    private function findImagesOrVideos(array $medias, string $type): ?array
    {
        $type = ($type === 'image') ? 3 : 4;
        $list = [];

        foreach ($medias as $key => $value) {
            if ($value->getType()->getId() === $type) {
                $list[] = $value->getFile();
            }
        }
        return $list;
    }
}
