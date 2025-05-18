<?php

namespace Avant\Zoho\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallbackAuthenticateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'state_confirmation' => session()->get('zoho.state'),
        ]);
    }

    public function rules(): array
    {
        return [
            'state' => ['required', 'string', 'confirmed'],
            'code'  => ['required', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        session()->forget('zoho.state');
    }
}
