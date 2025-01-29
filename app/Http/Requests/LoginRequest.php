<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mobile_no' => [
                'required',
                'regex:/^(98|97)\d{8}$/',
                'exists:users,mobile_no',
            ],
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Customize the validation error messages.
     */
    public function messages(): array
    {
        return [
            'mobile_no.required' => 'मोबाइल नम्बर आवश्यक छ।',
            'mobile_no.regex' => 'कृपया सही नेपाली मोबाइल नम्बर प्रविष्ट गर्नुहोस् (98 वा 97 बाट सुरु हुने 10 अंकको नम्बर)।',
            'mobile_no.exists' => 'यो मोबाइल नम्बर दर्ता गरिएको छैन।',
            'password.required' => 'पासवर्ड आवश्यक छ।',
            'password.min' => 'पासवर्ड कम्तीमा ६ अक्षरको हुनु पर्छ।',
        ];
    }

    /**
     * Ensure the request is not rate-limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'mobile_no' => 'तपाईंले धेरै प्रयास गर्नुभयो। कृपया केही समयपछि प्रयास गर्नुहोस्।',
            ])->status(429);
        }

        RateLimiter::hit($key, 60); // Limit expires after 60 seconds
    }

    /**
     * Authenticate the user.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!auth()->attempt($this->only('mobile_no', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'mobile_no' => 'प्रमाणीकरण असफल भयो। मोबाइल नम्बर वा पासवर्ड गलत छ।',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Get the rate limit key for the request.
     */
    public function throttleKey(): string
    {
        return strtolower($this->input('mobile_no')) . '|' . $this->ip();
    }
}
