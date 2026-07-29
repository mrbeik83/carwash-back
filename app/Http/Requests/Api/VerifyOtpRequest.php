<?php

namespace App\Http\Requests\Api;

use App\Enums\OtpPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'max:20'],
            'code' => ['required', 'digits:6'],
            'purpose' => ['nullable', Rule::enum(OtpPurpose::class)],
            'full_name' => ['nullable', 'string', 'max:150'],
        ];
    }
}
