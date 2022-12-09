<?php

namespace App\Traits;

use function auth;
use Carbon\Carbon;

trait ResponseTrait
{
    /**
     * Core of response
     *
     * @param string $message
     * @param array|object $data
     * @param integer $statusCode
     * @param boolean $isSuccess
     */
    public function coreResponse($data, $message, $statusCode, $isSuccess = true)
    {
        // Send the response
        if ($isSuccess) {
            $resData = [
                'error' => false,
                'message' => $message,
                'code' => $statusCode,
                'data' => $data,
            ];
        } else {
            $resData = [
                'error' => true,
                'message' => $message,
                'code' => $statusCode
            ];

            if ($data) $resData['data'] = $data;
        }
        return response()->json($resData, $statusCode)
            ->withHeaders([
                'Access-Control-Allow-Origin' => '*',
            ]);
    }

    /**
     * Send any token response
     *
     * @param string $message
     * @param array|object $data
     * @param integer $statusCode
     */
    protected function token($personalAccessToken, $message = null, $code = 200)
    {
        $user = auth()->user();
        $tokenData = [
            'access_token' => $personalAccessToken->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($personalAccessToken->token->expires_at)->toDateTimeString(),
            'user' => $user
        ];

        return $this->coreResponse($tokenData, $message, $code);
    }

    /**
     * Send any success response
     *
     * @param string $message
     * @param array|object|bool $data
     * @param integer $statusCode
     */
    public function success($data, $message, $statusCode = 200)
    {
        return $this->coreResponse($data, $message, $statusCode);
    }

    /**
     * Send any error response
     *
     * @param string $message
     * @param integer $statusCode
     */
    public function error($message, $statusCode = 500, $data = null)
    {
        return $this->coreResponse($data, $message, $statusCode, false);
    }
}
