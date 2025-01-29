<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMobileRequest extends FormRequest
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
            'mobile_no' => 'required|digits:10|exists:users,mobile_no', // Validation for mobile_no
        ];
    }

    public function messages()
    {
        return [
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.digits' => 'The mobile number must be 10 digits.',
            'mobile_no.exists' => 'The mobile number does not exist in our records.',
        ];
    }
}
