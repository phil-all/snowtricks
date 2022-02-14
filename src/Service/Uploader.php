<?php

namespace App\Service;

use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * A light uploader service
 */
class Uploader
{
    /**
     * @var string
     */
    private string $newFileName;

    /**
     * @var string
     */
    private string $destination;

    /**
     * @var string
     */
    private string $date;

    /**
     * Uploader constructor
     */
    public function __construct(string $uploadsDir)
    {
        $this->destination = $uploadsDir;
        $this->date = (new DateTime())->format('Y-m-d');
    }

    /**
     * Uploads image with new file name like 'uniqid-YYYY-mm-dd.extension'.
     *
     * @param UploadedFile $uploadedFile
     *
     * @return void
     */
    public function uploadImage(UploadedFile $uploadedFile): void
    {
        $this->newFileName = uniqid() . '-' . $this->date . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move($this->destination, $this->newFileName);
    }

    /**
     * Get getNewFileName value
     *
     * @return string
     */
    public function getNewFileName(): string
    {
        return $this->newFileName;
    }
}
