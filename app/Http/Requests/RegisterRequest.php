<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;

    private array $allowedRoles = [
        User::VISITOR_ROLE,
        User::MANAGER_ROLE,
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedRoles = implode(',', $this->allowedRoles);

        return [
            'first_name' => 'required|string|alpha|max:32',
            'last_name' => 'required|string|alpha|max:32',
            'email' => 'required|string|email|max:128|unique:users',
            'role' => "nullable|string|in:$allowedRoles",
            'password' => 'required|string|min:6|max:128',
        ];
    }

    public function messages(): array
    {
        $allowedRoles = implode(',', $this->allowedRoles);

        return [
            'first_name.required' => "Параметр ім'я є обов'язковим.",
            'first_name.alpha' => "Параметр ім'я має містити тільки літери.",
            'first_name.max' => "Параметр ім'я має бути не довше :max символів.",

            'last_name.required' => "Параметр прізвище є обов'язковим.",
            'last_name.alpha' => 'Параметр прізвище має містити тільки літери.',
            'last_name.max' => 'Параметр прізвище має бути не довше :max символів.',

            'email.required' => "Параметр е-мейл є обов'язковим.",
            'email.email' => 'Параметр е-мейл має неправильний формат.',
            'email.max' => 'Параметр е-мейл має бути не довше :max символів.',
            'email.unique' => 'Цей е-мейл вже зайнятий.',

            'role.in' => "Неправильна роль. Роль може бути тільки: $allowedRoles.",

            'password.required' => "Параметр пароль є обов'язковим.",
            'password.min' => 'Параметр пароль має бути не менше :min символів.',
            'password.max' => 'Параметр пароль має бути не довше :max символів.',
        ];
    }
}
