<?php

namespace App\Http\Controllers\Api\GPT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GPTService;
use App\Http\Requests\Api\GPT\GPTMessageRequest;
use App\Models\GPT\GTPMessage;
use Illuminate\Support\Facades\Auth;
class GPTController extends Controller
{
    protected $messageService;

    public function __construct(GPTService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function sendMessage(GPTMessageRequest $request)
    {
        $user = Auth::user();

        $response = $this->messageService->fetchData($request->input('message'));
        $content = $response['choices'][0]['message']['content'] ?? '';
        $cleanedContent = preg_replace('/[^\p{L}\p{N}\s\n.:;(){}\[\]]+/u', '', $content);
        GTPMessage::create([
            "user_id" => $user->id,
            "user_message" => $request->input('message'),
            "gpt_message" => $cleanedContent,
        ]);
        return response()->json([
            "status" => true,
            "message" => $cleanedContent,
        ]);
    }
}

