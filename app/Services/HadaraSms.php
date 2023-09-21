<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HadaraSms
{
    protected $baesUrl = 'http://smsservice.hadara.ps:4545/SMS.ashx/bulkservice/sessionvalue';
    protected $key;
    public function __construct($key)
    {
        $this->key = $key;
    }
    public function send($to, $message)
    {
        $response = Http::baseUrl($this->baesUrl)
            ->withHeaders([
                'x-api-key' => $this->key,
                // 'Authorization' => 'Bearer' . $this->key,
            ])
            ->withToken($this->key)
            ->get('sendmessage', [
                'apikey' => $this->key,
                'to' => $to,
                'msg' => $message
            ]);
        dd($response->body());
    }
}

?>
