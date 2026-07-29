<?php

namespace App\Http\Requests\CarWashPanel;

use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.members.invite') ?? false;
    }

    public function rules(): array
    {
        return [
            'mobile' => [
                'nullable',
                'required_without:email',
                'string',
                'max:20',
            ],
            'email' => [
                'nullable',
                'required_without:mobile',
                'email',
                'max:255',
            ],
            'role' => [
                'required',
                Rule::in(RoleName::carWashValues()),
            ],
        ];
    }
}
