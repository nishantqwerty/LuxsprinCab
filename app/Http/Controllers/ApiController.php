<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ApiController extends Controller
{
    /**
     * Get response
     * @param $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function result($message, $errors = [], $code = 200)
    {
        return response()
            ->json([
                'message' => $message,
                'errors' => [],
                'code' => $code
            ], $code);
    }


    /**
     * Get OK response
     * @param $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function result_ok($message, $data = [])
    {
        return response()
            ->json([
                'message' => $message,
                'data' => $data,
                'code' => 200
            ], 200);
    }

    public function result_message($data)
    {
        return response()
            ->json([
                'message' => $data,
                'code' => 200
            ], 200);
    }

    /**
     * Get FAILED response
     * @param $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function result_fail($errors = [], $code = 400)
    {
        return response()
            ->json([
                'message' => $errors,
                'code' => $code
            ], $code);
    }

    public function otp($phone,$otp)
    {
        $basic  = new \Vonage\Client\Credentials\Basic(env("VONAGE_API_KEY"), env("VONAGE_API_SECRET"));
        $client = new \Vonage\Client($basic);
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($phone, 'HCAB', "OTP to verify your mobile is $otp")
        );
        
        $message = $response->current();

        if ($message->getStatus() == 0) {
            return response()
                ->json([
                    'message' => 'The message was sent successfully.',
                    'code' => 200
                ], 200);
        } else {
            return response()
                ->json([
                    'message' => "The message failed with status: " . $message->getStatus(),
                    'code' => 400
                ], 400);
        }
    }
}
