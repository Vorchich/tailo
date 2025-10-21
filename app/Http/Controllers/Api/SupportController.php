<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminResponseRequest;
use App\Http\Requests\Api\ApplicationRequest;
use App\Http\Resources\Api\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $user = auth()->user()->loadMissing('applications');
        $applications = $user->applications;

        return response([
            'data' => [
                'applications' => ApplicationResource::collection($applications),
                'result' => true,
            ]]);
    }

    public function create(ApplicationRequest $request)
    {
        $data = $request->validated();

        $application = auth()->user()->applications()->create($data);

        return response([
            'data' => [
                'application' => ApplicationResource::make($application),
                'result' => true,
            ]]);
    }

    public function response(AdminResponseRequest $request, $application)
    {
        $application = Application::where('id', $application)->first();

        if(!$application){
            return response()->json(array(
                'data' => [
                'result' => false,
                'message'   =>  'Application not found',
                ]), 404);
        }

        $application->update($request->validated());

        return response([
            'data' => [
                'application' => ApplicationResource::make($application),
                'result' => true,
            ]]);
    }
}
