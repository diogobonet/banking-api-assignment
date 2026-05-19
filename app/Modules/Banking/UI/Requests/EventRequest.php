<?php

namespace App\Modules\Banking\UI\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:deposit,withdraw,transfer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'destination' => ['required_if:type,deposit', 'required_if:type,transfer', 'nullable', 'string'],
            'origin' => ['required_if:type,withdraw', 'required_if:type,transfer', 'nullable', 'string'],
        ];
    }
}
