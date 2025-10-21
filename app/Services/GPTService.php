<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GPTService
{
    protected $apiKey;
    public function __construct()
    {
        $this->apiKey = env('GPT_KEY');
    }

    public function fetchData($message)
    {
        $data = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
        ->post('https://api.openai.com/v1/chat/completions',[
            "model" => env('GPT_MODEL'),
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Я швея, тому прошу спілкуватися зі мною виключно на тему швейної справи. Я буду задавати питання, які стосуються пошиву одягу, і очікую від тебе конкретних, професійних порад і коментарів, пов'язаних з цією сферою. Відповідай максимально чітко, враховуючи всі нюанси, з актуальною інформацією і прикладами. Прошу уникати будь-яких тем, не пов'язаних зі швейною справою."
                ],
                [
                    "role" => "user",
                    "content" => $message
                ]
            ],
            "temperature" => 0.5,
            "max_tokens" => 200,
            "top_p" => 1.0,
            "frequency_penalty" => 0.52,
            "presence_penalty" => 0.5,
            "stop" => ["11."],
        ])->json();

        return $data;
    }
}
