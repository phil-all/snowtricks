<?php

namespace App\Service;

/**
 * A servcie to delete files
 */
class Eraser
{
    /**
     * Delete a file
     *
     * @param string $completeFilePath
     *
     * @return void
     */
    public function deleteFile(string $completeFilePath): void
    {
        if (file_exists($completeFilePath) && !is_dir($completeFilePath)) {
            unlink($completeFilePath);
        }
    }
}
