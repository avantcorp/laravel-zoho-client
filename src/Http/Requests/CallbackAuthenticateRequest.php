<?php

namespace Avant\ZohoClient\Http\Requests;

/**
 * @property string $state
 * @property string $code
 */
class CallbackAuthenticateRequest extends AuthenticateRequest
{
    public function authorize(): bool
    {
        return parent::authorize()
            && $this->state === session($this->getStateKey());
    }

    public function rules(): array
    {
        return [
            'state' => ['required', 'string'],
            'code'  => ['required', 'string'],
        ];
    }
}
