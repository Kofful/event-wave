<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetAllEventsRequest extends FormRequest
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
            'city_id' => 'integer|required|min:1',
            'event_type_id' => 'integer|nullable|min:1',
            'query' => 'string|nullable|max:255',
            'page' => 'integer|nullable|min:1',
            'date_from' => 'date|nullable',
            'date_to' => 'date|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.integer' => 'Параметр місто має бути числом.',
            'city_id.required' => "Параметр місто є обов'язковим.",
            'city_id.min' => 'Параметр місто має бути більше або дорівнювати 1.',

            'event_type_id.integer' => 'Параметр тип події має бути числом.',
            'event_type_id.min' => 'Параметр тип події має бути більше або дорівнювати 1.',

            'query.max' => 'Параметр пошуку має бути не довше 255 символів.',

            'page.integer' => 'Параметр сторінка має бути числом.',
            'page.min' => 'Параметр сторінка має бути більше або дорівнювати 1.',

            'date_from.date' => 'Параметр має бути датою.',
            'date_to.date' => 'Параметр має бути датою.',
        ];
    }
}
