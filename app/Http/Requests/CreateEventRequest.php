<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'city_id' => 'integer|required|min:1|exists:cities,id',
            'event_type_id' => 'integer|required|min:1|exists:event_types,id',
            'name' => 'string|required|max:64',
            'image' => 'image|required|max:10240',
            'date' => 'date|required|date_format:Y-m-d H:i|after:tomorrow',
            'description' => 'string|nullable',
            'notes' => 'string|nullable',
            'tickets' => 'array|required|max:10',
            'tickets.*.name' => 'string|required|max:32|distinct',
            'tickets.*.price' => 'integer|required|min:1|max:99999',
            'tickets.*.quantity' => 'integer|required|min:1|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.integer' => 'Параметр місто має бути числом.',
            'city_id.required' => "Параметр місто є обов'язковим.",
            'city_id.min' => 'Параметр місто має бути більше або дорівнювати 1.',
            'city_id.exists' => 'Параметр місто не відповідає жодному існуючому місту.',

            'event_type_id.integer' => 'Параметр тип події має бути числом.',
            'event_type_id.required' => "Параметр тип події є обов'язковим.",
            'event_type_id.min' => 'Параметр тип події має бути більше або дорівнювати 1.',
            'event_type_id.exists' => 'Параметр тип події не відповідає жодному існуючому типу.',

            'name.required' => "Параметр назва є обов'язковим.",
            'name.max' => "Параметр назва має бути не довше :max символів.",

            'image.image' => 'Параметр зображення має бути зображенням.',
            'image.required' => "Параметр зображення є обов'язковим.",
            'image.max' => 'Параметр зображення має бути не більше 10 МБ.',

            'date.date' => 'Параметр дата має бути датою.',
            'date.required' => "Параметр дата є обов'язковим.",
            'date.date_format' => 'Формат дати має бути Y-m-d H:i.',
            'date.after' => 'Параметр дата має бути після сьогоднішньої дати.',

            'tickets.array' => 'Параметр квитки має бути масивом.',
            'tickets.required' => "Параметр квитки є обов'язковим.",
            'tickets.max' => 'Кількість квитків повинна бути не більше :max.',

            'tickets.*.name.required' => "Параметр назва квитка є обов'язковим",
            'tickets.*.name.max' => 'Параметр назва квитка має бути не довше :max символів.',
            'tickets.*.name.distinct' => 'Параметр назва квитка має дублікати.',

            'tickets.*.price.integer' => 'Параметр ціна квитка має бути числом.',
            'tickets.*.price.required' => "Параметр ціна квитка є обов'язковим.",
            'tickets.*.price.min' => 'Параметр ціна квитка має бути не менше за :min.',
            'tickets.*.price.max' => 'Параметр ціна квитка має бути не більше за :max.',

            'tickets.*.quantity.integer' => 'Параметр кількість квитків має бути числом.',
            'tickets.*.quantity.required' => "Параметр кількість квитків є обов'язковим.",
            'tickets.*.quantity.min' => 'Параметр кількість квитків має бути не менше за :min.',
            'tickets.*.quantity.max' => 'Параметр кількість квитків має бути не більше за :max.',
        ];
    }
}
