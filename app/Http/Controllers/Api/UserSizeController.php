<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SizePdfRequest;
use App\Http\Requests\Api\UserSizeRequest;
use App\Http\Resources\Api\ProfileResource;
use App\Http\Resources\Api\SizeResource;
use App\Http\Resources\Api\UserSizeResource;
use App\Models\Category;
use App\Models\Notepad;
use App\Models\Size;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserSizeController extends Controller
{
    public function index()
    {
        $user = auth()->user()->loadMissing('sizes');

        return response([
            'data' => [
                'order' => UserSizeResource::collection($user->sizes),
                'result' => true,
            ]]);
    }

    public function update(UserSizeRequest $request)
    {

        $data = $request->validated();

        $measurements = $data['measurements'] ?? [];

        foreach($measurements as $key => $measurement)
        {
            $a[$key] = ['value' => $measurement];
        }

        auth()->user()->sizes()->syncWithoutDetaching($a);

        return response([
            'data' => [
                'profile' => ProfileResource::make(auth()->user()),
                'result' => true,
            ]]);
    }

    public function pdf(SizePdfRequest $request)
    {
        $elements = $request->sizes;

        $notepad = Notepad::find($request->notepad_id);

        $category_name = Category::find($request->category_id)->name ?? '';

        $pdf = Pdf::loadView('template.pdf', compact('elements', 'category_name'));

        $tempPdfPath = storage_path('app/temp_pdf_' . Str::random(10) . '.pdf');
        file_put_contents($tempPdfPath, $pdf->output());

        if($notepad)
        {
            $notepad->addMedia($tempPdfPath)
                ->usingName('Розміри ' . now()->format('Y-m-d H:i'))
                ->toMediaCollection('files', 'notepads');
        }

        $pdf = Pdf::loadView('template.pdf', compact('elements', 'category_name'));

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->stream();
        }, 'document.pdf');

    }

    public function sizes()
    {
        $sizes = Size::get();

        return response([
            'data' => [
                'sizes' => SizeResource::collection($sizes),
                'result' => true,
        ]]);
    }
}
