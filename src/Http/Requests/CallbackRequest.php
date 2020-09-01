<?php

namespace Avant\LaravelZohoClient\Http\Requests;

/**
 * @property string $state
 * @property string $code
 */
class CallbackRequest extends Request
{
    public function authorize(): bool
    {
        return auth()->check()
            && $this->state === session($this->getStateKey());
    }

    public function rules(): array
    {
        return [
            'state' => 'required',
            'code'  => 'required',
        ];
    }
}
