<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => "Параметр е-мейл є обов'язковим.",
            'email.email' => 'Параметр е-мейл має неправильний формат.',

            'password.required' => "Параметр пароль є обов'язковим.",
        ];
    }
}
