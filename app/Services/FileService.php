<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function save($model, $recordedFile, $newFile, $disk, $type = null, $priority = 0)
    {
        if ($recordedFile) {
            return $this->update($recordedFile, $newFile);
        }
        $src = $newFile->store($model->id, $disk);
        $file = new File([
            'path' => $src,
            'disk' => $disk,
            'mime_type' => $newFile->getMimeType(),
            'type' => $type,
            'priority' => $priority,
        ]);

        return $file;
    }

    public function update(File $recordedFile, $file)
    {
        if ($recordedFile->path) {
            Storage::disk($recordedFile->disk)->delete('/' . $recordedFile->path);
        }
        $recordedFile->path = $file->store($recordedFile->id, $recordedFile->disk);
        $recordedFile->save();
        return $recordedFile;
    }
}

