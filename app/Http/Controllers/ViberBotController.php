<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ViberBotController extends Controller
{

    /**
     * edit to use PALM_API_TEXT_ENDPOINT or PALM_API_CHAT_ENDPOINT endpoint
     */

    private $palm_endpoint = 'PALM_API_CHAT_ENDPOINT';


    // call to set weebhook
    public function setWebhook()
    {
        $client = new Client();

        $url = 'https://chatapi.viber.com/pa/set_webhook';
        $headers = [
            'X-Viber-Auth-Token' => env('VIBER_KEY'),
            'ngrok-skip-browser-warning' => '69420' // prevent the ngrok browser security
        ];
        $body = [
            'url' => env('NGROK_ENDPOINT'), // your ngrok url
        ];

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        // Log the response for debugging
        Log::channel('viber')->info($response->getBody()->getContents());

        return response()->json(['status' => 'ok']);
    }




    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::channel('viber')->info(json_encode($data)); // Log the incoming data for debugging

        if($data['event'] === 'subscribed'){
            if(Cache::has("subscriber_{$data['user']['id']}")){
                return response()->json(['status' => 'ok']);
            }

            $this->subscribed($data);
        }

        if(isset($data['event']['delivered']) || isset($data['event']['seen'])){
            return;
        }

        if (isset($data['message']['text'])) {
            $message = $data['message']['text'];
            $sender = $data['sender']['id'];
            // $name = $data['sender']['name'];

            // PaLM 2 Method
            $response = $this->generateMessage($message);
            $this->sendMessage($sender, $response); // send the message
        }

        return response()->json(['status' => 'ok']);
    }

    public function subscribed($request){
        $event = $request['event'];
        $userId = $request['user']['id'];
        $name = $request['user']['name'];
        $avatar = $request['user']['avatar'];
        $language = $request['user']['language'];
        $country = $request['user']['country'];


        $message = "Welcome {$name} to Sample Bot! with PaLM2 Integration.";
        Cache::put("subscriber_{$userId}", true,now()->addSecond(30));
        $this->sendMessage($userId,$message);
        return;
    }



    public function sendMessage($receiver, $text)
    {
        $client = new Client();

        $url = 'https://chatapi.viber.com/pa/send_message';
        $headers = [
            'X-Viber-Auth-Token' => env('VIBER_KEY'), // Replace with your Viber auth token
            'ngrok-skip-browser-warning' => '69420'
        ];
        $body = [
            'receiver' => $receiver,
            'type' => 'text',
            'text' => $text,
        ];

        $response = $client->post($url, [
            'headers' => $headers,
            'json' => $body,
        ]);

        // Log the response for debugging
        Log::channel('viber')->info($response->getBody()->getContents());
    }


    /**
     *
     * function to call the PaLM2 API
     * @param string $msg user input from viber chat
     * @return string reponse of PaLM2 API
     */


    public function generateMessage($msg)
    {
        $client = new Client();// make a new guzzle Clietn


        // check if endpoint is for chat endpoint or text base endpoint
        $prompt = ($this->palm_endpoint === 'PALM_API_CHAT_ENDPOINT') ?
        [
            'prompt' => [
                'messages' => [
                    ['content'  => $msg],
                ],
            ],
            'temperature'       => 0.5,
            'candidate_count'   => 1,
        ] : [
            'prompt'   => [
                'text'          => $msg
            ],
            'maxOutputTokens'   => 300,
            'temperature'       => 0.5,
        ];

        try {
            $response = $client->post(env($this->palm_endpoint), [
                'headers' => ['Content-Type' => 'application/json'],
                'query' => ['key' => env('PALM_API_KEY')],
                'json' => $prompt,
            ]);

            $responseData = json_decode($response->getBody(), true);
            Log::channel('viber')->info('PaLM2 API Response: ' . json_encode($responseData));
            // return response()->json($responseData['candidates'][0]['content']);
            return ($this->palm_endpoint === 'PALM_API_CHAT_ENDPOINT') ? $responseData['candidates'][0]['content'] : $responseData['candidates'][0]['output'];

        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            $statusCode = $exception->getResponse()->getStatusCode();
            $message = json_decode($exception->getResponse()->getBody(), true)['error']['message'] ?? $exception->getMessage();

            return "There is something wrong with your message.";
        } catch (\Exception $exception) {
            return "There is something wrong with your message.";
            // return
        }
    }
}



