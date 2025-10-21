<?php

namespace App\Services;

use App\Http\Requests\Api\AddTextRequest;
use App\Http\Requests\Api\CreateTextRequest;
use App\Http\Requests\Api\UpdateTextRequest;
use App\Models\File;
use App\Models\Notepad;
use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotepadService
{
    public function createText(AddTextRequest $request, $model):Text
    {
        $text = $model->texts()->create(
            $request->validated()
        );

        return $text;
    }

    public function updateText(UpdateTextRequest $request, Text $text):Text
    {
        $text->update(
            $request->validated()
        );

        return $text->fresh();
    }

    public function addFile($request, $model,  string $disk = 'notepads')
    {
        $file = $model->addMedia($request->file)
            ->usingName($request->name)
            ->toMediaCollection('files', $disk);

        return $file;
    }

    public function handleFiles($model, Request $request, string $disk = 'notepads')
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $model->addMedia($file)->toMediaCollection('files', $disk);
            }
        }
    }

    public function updateFile($file,  string $name)
    {
        $file->name = $name;
        $file->save();

        return $file;
    }

    public function isAuthorize(User $user, Notepad $notepad): void
    {
        $this->isSeamstressNotebook($user, $notepad);
    }

    public function authorizeSeamstress($user): void
    {
        if (!$user->is_seamstress) {
            abort(422, 'You are not a seamstress!');
        }
    }

    private function isSeamstressNotebook(User $user,  Notepad $notepad): void
    {
        if ($user->id !== $notepad->user_id) {
            abort(422, 'This is not your notebook!');
        }
    }
}

