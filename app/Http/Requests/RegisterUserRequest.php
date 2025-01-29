<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile_no' => ['required','numeric','regex:/^(\+977|0)?(98|97|96|95|94|93|92|91|90)\d{8}$/', 'unique:users'],
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'नाम आवश्यक छ।',
            'name.string' => 'नाम केवल स्ट्रिङ हुनु पर्छ।',
            'mobile_no.required' => 'मोबाइल नम्बर आवश्यक छ।',
            'mobile_no.regex' => 'कृपया सही नेपाली मोबाइल नम्बर प्रविष्ट गर्नुहोस् (98 वा 97 बाट सुरु हुने 10 अंकको नम्बर)।',
            'mobile_no.unique' => 'यो मोबाइल नम्बर पहिल्यै दर्ता गरिएको छ।',
            'password.required' => 'पासवर्ड आवश्यक छ।',
            'password.min' => 'पासवर्ड कम्तीमा ६ अक्षरको हुनु पर्छ।',
            'password.confirmed' => 'पासवर्ड पुष्टि मेल खाँदैन।',
        ];
    }
}
