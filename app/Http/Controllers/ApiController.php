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
use Twilio\Rest\Client;

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

    public function otp($phone, $otp)
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

    public function us_otp($phone, $otp)
    {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");
        $twilio_number = env("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $response = $client->messages->create(
            $phone,
            ['from' => $twilio_number, 'body' => "OTP to verify your mobile is $otp"]
        );
        if ($response) {
            return response()
                ->json([
                    'message' => 'The message was sent successfully.',
                    'code' => 200
                ], 200);
        } else {
            return response()
                ->json([
                    'message' => "The message failed to send",
                    'code' => 400
                ], 400);
        }
    }

    public function sendNotificationAndroid($requestedData)
    {
        // $API_ACCESS_KEY = 'AIzaSyBXh7HBXsLGzZDeomvWliryh7sgmHEMtm4';
        $API_ACCESS_KEY = env('FIREBASE_SERVER_KEY');
        // $API_ACCESS_KEY = 'AAAAQh48WTQ:APA91bEplmbgrGW-weJ799CibrhcBukNZUvVn6r3UNGdkjmYCl_exHOkya-AA4GPvOt6CDAhq2-zXyyRiuxbC3poYRGv0e3VY7Rg1ldf43B1w5ytYVqjF0mv56DcLChxJtwuWgf9avqG';

        $message = $requestedData['message'];
        $deviceToken = $requestedData['device_token'];
        $msg =  ["bookingId" => $requestedData['booking_id'], "userId" => $requestedData['id'], "name"   =>  $requestedData['user_name'], "profile_picture" => $requestedData['user_image'], "deviceToken" => $requestedData['device_token'], "message" => $requestedData['message'], "bookingData" => $requestedData['booking_data'], "distance" => $requestedData['distance']];

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => 'Booking Request',
            'body' => 'New Booking Request',
            'sound' => 'default',
            'badge' => '1'
        ];

        if (isset($requestedData['from_user_name'])) {
            // if chat message to send , then title will be from user name
            // $notification['title'] = $requestedData['from_user_name'];
            $notification['title'] = 'New Booking Request';
        }

        $extraNotificationData = ["message" => $notification, "extra_data" => $requestedData, 'booking_id' => $requestedData['booking_id'], 'booking_data' => $requestedData['booking_data'], 'distance'    => $requestedData['distance']];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $deviceToken, //single token
            'notification' => $notification,
            'data' => $msg,
            'priority' => 'high'
        ];

        $headers = [
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        // echo $result;

    }

    public function sendAcceptNotification($requestedData)
    {
        // $API_ACCESS_KEY = 'AIzaSyBXh7HBXsLGzZDeomvWliryh7sgmHEMtm4';
        $API_ACCESS_KEY = env('FIREBASE_SERVER_KEY');
        // $API_ACCESS_KEY = 'AAAAQh48WTQ:APA91bEplmbgrGW-weJ799CibrhcBukNZUvVn6r3UNGdkjmYCl_exHOkya-AA4GPvOt6CDAhq2-zXyyRiuxbC3poYRGv0e3VY7Rg1ldf43B1w5ytYVqjF0mv56DcLChxJtwuWgf9avqG';

        $message = $requestedData['message'];
        $deviceToken = $requestedData['device_token'];
        $msg =  ["bookingId" => $requestedData['booking_id'], "driverId" => $requestedData['id'], "name"   =>  $requestedData['driver_name'], "profile_picture" => $requestedData['driver_image'], "deviceToken" => $requestedData['device_token'], "message" => $requestedData['message']];

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => 'Booking Request',
            'body' => $message,
            'sound' => 'default',
            'badge' => '1'
        ];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $deviceToken, //single token
            'notification' => $notification,
            'data' => $msg,
            'priority' => 'high'
        ];

        $headers = [
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        // echo $result;
    }

    public function sendNotificationIos($requestedData)
    {
        try {
            $message = $requestedData['message'];
            $deviceToken = $requestedData['device_token'];

            $body['aps'] = array(
                'alert' => trim($message),
                'sound' => 'default',
                'badge' => "+1",
                'extra_data' => $requestedData,
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            $msg = chr(0) . pack('n', 32) . pack('H*', trim($deviceToken)) . pack('n', strlen($payload)) . $payload;
            $ctx = stream_context_create();

            // For sandbox
            //$url = 'ssl://gateway.sandbox.push.apple.com:2195';
            //stream_context_set_option($ctx, 'ssl', 'local_cert', public_path().'/notification/MFCertificates.pem');

            // for production
            $url = 'ssl://gateway.push.apple.com:2195';
            $passphrase = '123';
            stream_context_set_option($ctx, 'ssl', 'local_cert', public_path() . '/notification/VV_PushCert_Prod.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Connection to APNS server
            // $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

            if (!$fp) {
                return true;
                //exit("Failed to connect: $err $errstr" . PHP_EOL);
            }

            // Send it to the server
            try {
                $result = fwrite($fp, $msg, strlen($msg));

                stream_set_blocking($fp, 0);
                return true;
            } catch (Exception $ex) {
                // try once again for socket busy error (fwrite(): SSL operation failed with code 1.
                // OpenSSL Error messages:\nerror:1409F07F:SSL routines:SSL3_WRITE_PENDING)
                sleep(1); //sleep for 1 seconds
                $result = fwrite($fp, $msg, strlen($msg));

                stream_set_blocking($fp, 0);
                return true;
            }


            // if (!$result)
            // {
            //     //echo 'Message not delivered' . PHP_EOL;
            // }else
            // {
            //     //echo 'Message successfully delivered' . PHP_EOL;

            // }

            // return $result;

            // Close the connection to the server
            fclose($fp);
        } catch (Exception $eh) {
            return true;
        }
    }
}
