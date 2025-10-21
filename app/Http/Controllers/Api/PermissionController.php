<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PermissionRequest;
use App\Http\Resources\Api\NotepadResource;
use App\Http\Resources\Api\PermissionResource;
use App\Models\Notepad;
use App\Models\Permission;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function setPermission(PermissionRequest $request, User $user)
    {
        $permission = $user->permissions()->updateOrCreate([
            'permissible_id' => $request->permissible_id,
            'permissible_type' => 'App\Models\\' . $request->permissible_type,
        ],[
            'can_edit' => $request->can_edit,
        ]);

        return response([
            'data' => [
                'permissions' => PermissionResource::make($permission),
                'result' => true,
            ]
        ]);
    }

    public function delete(User $user, string $model, int $id)
    {
        $allowedModels = ['Notepad', 'NotepadFolder', 'Text', 'Media'];

        if (!in_array($model, $allowedModels)) {
            return response()->json(['message' => 'The model is wrong!'], 422);
        }

        $modelClass = "App\\Models\\$model";

        $deleted = $user->permissions()
            ->where('permissible_id', $id)
            ->where('permissible_type', $modelClass)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Permission not found!'], 422);
        }

        return response()->json([
            'message' => 'Permission deleted successfully',
            'result' => true,
        ]);
    }

    public function getNotepads()
    {
        $user = auth()->user();
        $notepads = $user->notepadPermissions()
        ->with(['permissible' => function ($query) use ($user) {
            $query->with([
                'texts' => function ($q) use ($user) {
                    $q->whereHas('permissions', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
                },
                'media' => function ($q) use ($user) {
                    $q->whereHas('permissions', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
                },
                'notepadFolders' => function ($q) use ($user) {
                    $q->whereHas('permissions', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
                }
            ]);
        }])
        ->get()
        ->pluck('permissible');

        return response([
            'data' => [
                'notepads' => NotepadResource::collection($notepads),
                'result' => true,
            ]
        ]);
    }

    public function getNotepad($notepad)
    {
        $user = auth()->user();

        $notepad = $user->notepadPermissions()
            ->where('permissible_id', $notepad)
            ->where('permissible_type', Notepad::class)
            ->with(['permissible' => function ($query) use ($user) {
                $query->with([
                    'texts' => function ($q) use ($user) {
                        $q->whereHas('permissions', function ($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        });
                    },
                    'media' => function ($q) use ($user) {
                        $q->whereHas('permissions', function ($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        });
                    },
                    'notepadFolders' => function ($q) use ($user) {
                        $q->whereHas('permissions', function ($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        });
                    }
                ]);
            }])
            ->firstOrFail();
            // dd($notepad->permissible);
            return response([
                'data' => [
                    'notepads' => NotepadResource::make($notepad->permissible),
                    'result' => true,
                ]
            ]);
    }
}
