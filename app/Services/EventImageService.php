<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventImageService
{
    public function getFileName(UploadedFile $uploadedFile): string
    {
        $timestamp = microtime(true) * 10000;
        $fileName = dechex(intval($timestamp));
        $extension = $uploadedFile->getClientOriginalExtension();

        $fullFileName = "$fileName.$extension";

        return $fullFileName;
    }

    public function saveFile(UploadedFile $uploadedFile, string $fileName): void
    {
        $fileContents = file_get_contents($uploadedFile->getRealPath());

        $storage = Storage::disk('event_images');

        $storage->put($fileName, $fileContents);
    }

    public function deleteFile(string $fileName): void
    {
        $storage = Storage::disk('event_images');
        $storage->delete($fileName);
    }
}
