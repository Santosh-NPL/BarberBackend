<?php

namespace App\Http\Controllers\API;

use App\Helpers\SmsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyMobileRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginRequest $request)
    {
        Log::info($request->all());

        // Authenticate the user
        $request->authenticate();

        $user = auth()->user();

        Log::info($user);
        $token = $user->createToken('authToken')->plainTextToken;
        // Extract only permission names
        $permissions = $user->allPermissions()->pluck('name');
        // Send success response
        return $this->success(
            'Login successful',
            [
                'user' => $user,
                'roles' => $user->getRoles(),
                'permission' => $permissions,
                'token' => $token,
            ],
            200
        );
    }

    public function verifyMobile(VerifyMobileRequest $request)
    {
        Log::info($request->all());
        // If validation passes, the code continues here
        $mobile_no = $request->mobile_no;

        // Generate a 6-digit random token
        $token = rand(100000, 999999);

        // Save or update token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['mobile_no' => $mobile_no],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(2),
            ]
        );

        //Dear Customer, Please use OTP for further processing, it will be valid for two minutes.
        $message = "Dear Customer, Please use OTP $token for further processing, it will be valid for two minutes.";

        if (SmsHelper::sendSms($mobile_no, $message)) {
            return $this->success('OTP sent successfully to your mobile number.', ['mobile_no' => $request->mobile_no], 201);
        } else {
            return $this->error('Failed to send OTP. Please try again.', 'Error', 500);
        }
    }

    public function OtpVerify(Request $request)
    {
        Log::info($request->all());
        // Validate the incoming request
        $request->validate([
            'mobile_no' => 'required|string',
            'otp' => 'required|numeric',
        ]);

        $mobile_no = $request->mobile_no;
        $otp = $request->otp;

        // Check if the OTP exists and is not expired
        $otpRecord = DB::table('password_reset_tokens')
            ->where('mobile_no', $mobile_no)
            ->where('token', $otp)
            ->first();

        if (!$otpRecord) {
            return $this->error('Invalid OTP.', 'Error', 400);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($otpRecord->expires_at))) {
            return $this->error('OTP has expired.', 'Error', 400);
        }

        return $this->success('OTP verified successfully.');
    }

    public function updatePassword(Request $request)
    {
        Log::error($request->all());
        // Validate the incoming request
        $request->validate([
            'mobile_no' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $mobile_no = $request->mobile_no;
        $new_password = $request->password;

        // Update the password in the users table
        $user = User::where('mobile_no', $mobile_no)->first();
        if (!$user) {
            return $this->error('User not found.', 'Error', 404);
        }
        Log::error($user);

        $user->password = Hash::make($new_password);
        $user->save();

        // Delete the OTP record after successful password reset
        DB::table('password_reset_tokens')
            ->where('mobile_no', $mobile_no)
            ->delete();

        return $this->success('Password updated successfully.');
    }

    /**
     * Handle login failure response (Optional, used internally in LoginRequest if needed).
     */
    public function failedLogin()
    {
        return $this->error(
            'Invalid credentials',
            ['mobile_no' => 'The mobile number or password is incorrect'],
            401
        );
    }

    public function __invoke(Request $request)
    {
        // TODO: Implement __invoke() method.
        $request->user()->currentAccessToken()->delete();

        return $this->success('Successfully logged out from this device', [], 200);

    }
}
