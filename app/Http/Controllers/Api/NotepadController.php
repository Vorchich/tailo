<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddFileRequest;
use App\Http\Requests\Api\AddFilesRequest;
use App\Http\Requests\Api\AddNotepadRequest;
use App\Http\Requests\Api\AddTextRequest;
use App\Http\Requests\Api\UpdateFileRequest;
use App\Http\Requests\Api\UpdateFilesRequest;
use App\Http\Requests\Api\UpdateNotepadRequest;
use App\Http\Requests\Api\UpdateTextRequest;
use App\Http\Resources\Api\MediaResource;
use App\Http\Resources\Api\NotepadResource;
use App\Http\Resources\Api\TextResource;
use App\Models\Notepad;
use App\Models\Text;
use App\Services\NotepadService;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class NotepadController extends Controller
{
    private $notepadService;

    public function __construct(NotepadService $notepadService)
    {
        $this->notepadService = $notepadService;
    }

    public function index()
    {
        return response([
            'data' => [
                'notepads' => NotepadResource::collection(auth()->user()->notepads()->get()),
                'result' => true,
            ]
        ]);
    }

    public function show(Notepad $notepad)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepad->loadMissing('notepadFolders', 'texts', 'sizes');

        return response([
            'data' => [
                'notepads' => NotepadResource::make($notepad),
                'result' => true,
            ]
        ]);
    }

    public function create(AddNotepadRequest $request)
    {
        $user = auth()->user();

        $notepad = $user->notepads()->create($request->validated());

        return response([
            'data' => [
                'notepads' => NotepadResource::make($notepad),
                'result' => true,
            ]
        ]);
    }

    public function edit(UpdateNotepadRequest $request, Notepad $notepad)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepad->update($request->validated());

        return response([
            'data' => [
                'notepads' => NotepadResource::make($notepad->fresh()),
                'result' => true,
            ]
        ]);
    }

    public function delete(Notepad $notepad)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);

        $notepad->delete();

        return response()->json([
            'message' => 'Notepad deleted successfully',
            'result' => true,
        ]);
    }

    public function createText(AddTextRequest $request, Notepad $notepad)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $text = $this->notepadService->createText($request, $notepad);

        return response([
            'data' => [
                'text' => TextResource::make($text),
                'result' => true,
            ]
        ]);
    }

    public function editText(UpdateTextRequest $request, Notepad $notepad, Text $text)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $text = $this->notepadService->updateText($request, $text);

        return response([
            'data' => [
                'text' => TextResource::make($text),
                'result' => true,
            ]
        ]);
    }

    public function deleteText(Notepad $notepad, Text $text)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);

        $notepad->texts()->findOrFail($text->id)->delete();

        return response()->json([
            'message' => 'Notepad text deleted successfully',
            'result' => true,
        ]);
    }

    public function createFile(Notepad $notepad, AddFileRequest $request)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $file = $this->notepadService->addFile($request, $notepad, 'notepads');

        return response()->json([
            'file' => MediaResource::make($file),
            'result' => true,
        ]);
    }

    public function updateFile(UpdateFileRequest $request, Notepad $notepad, Media $file)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        // dd($file);
        $file = $this->notepadService->updateFile($file, $request->name);

        return response()->json([
            'file' => MediaResource::make($file),
            'result' => true,
        ]);
    }

    public function createFiles(Notepad $notepad, AddFilesRequest $request)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $this->notepadService->handleFiles($notepad, $request, 'notepads');

        return response()->json([
            'notepad' => NotepadResource::make($notepad),
            'result' => true,
        ]);
    }

    public function deleteFile(Notepad $notepad, $file)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $file = $notepad->getMedia('files')->firstWhere('id', $file);

        if (!$file) {
            abort(404, 'File not found!');
        }
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
            'result' => true,
        ]);
    }
}
