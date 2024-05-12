<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateEventRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;

    public function authorize(): bool
    {
        return Gate::allows('update', $this->event);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $previousDate = $this->event->date;

        return [
            'city_id' => 'integer|nullable|min:1|exists:cities,id',
            'event_type_id' => 'integer|nullable|min:1|exists:event_types,id',
            'name' => 'nullable|string|max:64',
            'image' => 'image|nullable|max:10240',
            'date' => "date|nullable|date_format:Y-m-d H:i|after:$previousDate",
            'description' => 'string|nullable',
            'notes' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.integer' => 'Параметр місто має бути числом.',
            'city_id.min' => 'Параметр місто має бути більше або дорівнювати 1.',
            'city_id.exists' => 'Параметр місто не відповідає жодному існуючому місту.',

            'event_type_id.integer' => 'Параметр тип події має бути числом.',
            'event_type_id.min' => 'Параметр тип події має бути більше або дорівнювати 1.',
            'event_type_id.exists' => 'Параметр тип події не відповідає жодному існуючому типу.',

            'name.string' => "Параметр назва має бути рядком.",
            'name.max' => "Параметр назва має бути не довше :max символів.",

            'image.image' => 'Параметр зображення має бути зображенням.',
            'image.max' => 'Параметр зображення має бути не більше 10 МБ.',
            'image.uploaded' => 'Не вдалось завантажити зображення.',

            'date.date' => 'Параметр дата має бути датою.',
            'date.date_format' => 'Формат дати має бути Y-m-d H:i.',
            'date.after' => 'Параметр дата має бути після поточної дати події.',
        ];
    }
}
