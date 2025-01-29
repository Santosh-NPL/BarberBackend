<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsHelper{
    public static function sendSms($to, $text)
    {

        return true;

        $authToken = env('AAKASH_SMS_AUTH_TOKEN'); // Replace with your token or set in .env
        $url = "https://sms.aakashsms.com/sms/v3/send/";

        $args = http_build_query([
            'auth_token' => $authToken,
            'to'         => $to,
            'text'       => $text,
        ]);

        try {
            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute and fetch the response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                Log::error('SMS API cURL Error: ' . curl_error($ch));
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            // Parse the response
            $responseData = json_decode($response, true);

            // Log the response for debugging
            Log::info('SMS API Response: ', $responseData);

            // Check if the SMS was successfully sent
            if (isset($responseData['error']) && $responseData['error'] === false) {
                return true;
            } else {
                $errorMessage = $responseData['message'] ?? 'Unknown error';
                Log::error("SMS API Error: $errorMessage");
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending SMS: ' . $e->getMessage());
            return false;
        }
    }
}
