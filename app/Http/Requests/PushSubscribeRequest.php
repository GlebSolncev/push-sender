<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushSubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string', 'min:10'],
            'publicKey' => ['required', 'string', 'min:10'],
            'authToken' => ['required', 'string', 'min:10'],
            'contentEncoding' => ['string', 'nullable'],
        ];

    }
}
