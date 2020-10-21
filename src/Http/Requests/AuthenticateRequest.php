<?php

namespace Avant\ZohoClient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules()
    {
        return [];
    }

    public function getStateKey(): string
    {
        return 'zoho_client.state.'.$this->user()->getKey();
    }
}
