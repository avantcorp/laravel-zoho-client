<?php

namespace Avant\LaravelZohoClient\Http\Requests;

use Avant\LaravelZohoClient\ZohoClientServiceProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class Request extends FormRequest
{
    public function getStateKey(): string
    {
        return ZohoClientServiceProvider::TAG . '.state.' . Hash::make($this->user()->getKey());
    }
}
