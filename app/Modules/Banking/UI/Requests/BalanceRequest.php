<?php

namespace App\Modules\Banking\UI\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BalanceRequest extends FormRequest
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
            'account_id' => ['required', 'string'],
        ];
    }

    /**
     * @param Validator $validator
     * @return never
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json(0, 404));
    }
}
