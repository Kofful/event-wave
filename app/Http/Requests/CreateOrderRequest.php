<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    // TODO add validation

    public function rules(): array
    {
        return [
            'ticket_id' => 'required|integer|exists:tickets,id',
            'first_name' => 'required|string|alpha|max:32',
            'last_name' => 'required|string|alpha|max:32',
            'email' => 'required|string|email|max:128',
        ];
    }

    public function messages(): array
    {
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
        ];
    }
}
