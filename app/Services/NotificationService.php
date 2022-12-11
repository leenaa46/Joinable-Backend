<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\BaseService;

class NotificationService extends BaseService
{
    /**
     * send notification to all users
     */
    public function sendNotification($title, $body, $postId, $personalId = null)
    {
        $url = \config('services.firebase.url');
        $key = "key=" . \config('services.firebase.key');

        $body = [
            "notification" => [
                "body" => $title,
                "title" => $body
            ],
            "data" => [
                "post_id" => $postId,
                "personal_id" => $personalId,
            ],
            "chanel" => "basic_channel",
            "to" => "/topics/all"
        ];

        Http::withHeaders([
            'Authorization' =>  $key
        ])->post($url, $body);
    }
}
