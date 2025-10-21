<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddFileRequest;
use App\Http\Requests\Api\AddFilesRequest;
use App\Http\Requests\Api\AddNotepadFolderRequest;
use App\Http\Requests\Api\AddTextRequest;
use App\Http\Requests\Api\UpdateFileRequest;
use App\Http\Requests\Api\UpdateNotepadFolderRequest;
use App\Http\Requests\Api\UpdateTextRequest;
use App\Http\Resources\Api\MediaResource;
use App\Http\Resources\Api\NotepadFolderResource;
use App\Http\Resources\Api\TextResource;
use App\Models\Notepad;
use App\Models\NotepadFolder;
use App\Models\Text;
use App\Services\NotepadService;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class NotepadFolderController extends Controller
{
    private $notepadService;

    public function __construct(NotepadService $notepadService)
    {
        $this->notepadService = $notepadService;
    }

    public function index(Notepad $notepad)
    {

        $notepad->loadMissing('notepadFolders');

        return response([
            'data' => [
                'notepadFolders' => NotepadFolderResource::collection($notepad->notepadFolders),
                'result' => true,
            ]
        ]);
    }

    public function show(Notepad $notepad, NotepadFolder $notepadFolder)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id)->loadMissing('texts');;

        return response([
            'data' => [
                'notepadFolders' => NotepadFolderResource::make($notepadFolder),
                'result' => true,
            ]
        ]);
    }

    public function create(AddNotepadFolderRequest $request, Notepad $notepad, NotepadFolder $notepadFolder)
    {
        $user = auth()->user();
        $this->notepadService->isAuthorize($user, $notepad);
        $notepadFolder = $notepad->notepadFolders()->create($request->validated());

        $this->notepadService->handleFiles($notepadFolder, $request, 'notepadFiles');

        return response([
            'data' => [
                'notepadsFolders' => NotepadFolderResource::make($notepadFolder),
                'result' => true,
            ]
        ]);
    }

    public function edit(UpdateNotepadFolderRequest $request, Notepad $notepad, NotepadFolder $notepadFolder)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepad
            ->notepadFolders()
            ->findOrFail($notepadFolder->id)
            ->update($request->validated());

        return response([
            'data' => [
                'notepadsFolders' => NotepadFolderResource::make($notepadFolder->fresh()),
                'result' => true,
            ]
        ]);
    }

    public function delete(Notepad $notepad, NotepadFolder $notepadFolder)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);

        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id)->delete();

        return response()->json([
            'message' => 'Notepad deleted successfully',
            'result' => true,
        ]);
    }

    public function createText(AddTextRequest $request, Notepad $notepad, NotepadFolder $notepadFolder)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);
        $text = $this->notepadService->createText($request, $notepadFolder);

        return response([
            'data' => [
                'text' => TextResource::make($text),
                'result' => true,
            ]
        ]);
    }

    public function editText(UpdateTextRequest $request, Notepad $notepad, NotepadFolder $notepadFolder, Text $text)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);
        $text = $this->notepadService->updateText($request, $text);

        return response([
            'data' => [
                'text' => TextResource::make($text),
                'result' => true,
            ]
        ]);
    }

    public function deleteText(Notepad $notepad, NotepadFolder $notepadFolder, Text $text)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);

        $notepadFolder->texts()->findOrFail($text->id)->delete();

        return response()->json([
            'message' => 'Notepad text deleted successfully',
            'result' => true,
        ]);
    }

    public function createFile(Notepad $notepad, NotepadFolder $notepadFolder, AddFileRequest $request)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);
        $file = $this->notepadService->addFile($request, $notepadFolder, 'notepadFiles');

        return response()->json([
            'file' => MediaResource::make($file),
            'result' => true,
        ]);
    }

    public function updateFile(UpdateFileRequest $request, Notepad $notepad, NotepadFolder $notepadFolder, Media $file)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);
        $file = $this->notepadService->updateFile($file, $request->name);

        return response()->json([
            'file' => MediaResource::make($file),
            'result' => true,
        ]);
    }

    public function createFiles(Notepad $notepad, NotepadFolder $notepadFolder, AddFilesRequest $request)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);
        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);
        $this->notepadService->handleFiles($notepadFolder, $request, 'notepadFiles');

        return response()->json([
            'notepadsFolders' => NotepadFolderResource::make($notepadFolder),
            'result' => true,
        ]);
    }

    public function deleteFile(Notepad $notepad, NotepadFolder $notepadFolder, $file)
    {
        $this->notepadService->isAuthorize(auth()->user(), $notepad);

        $notepadFolder = $notepad->notepadFolders()->findOrFail($notepadFolder->id);

        $file = $notepadFolder->getMedia('files')->firstWhere('id', $file);

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
