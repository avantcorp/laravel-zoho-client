<?php

namespace Avant\ZohoClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @property int $id
 * @property int $user_id
 * @property \League\OAuth2\Client\Token\AccessTokenInterface $token
 * @property string $refresh_token
 * @property $user
 */
class ZohoAccessToken extends Model
{
    protected $fillable = [
        'user_id', 'token', 'refresh_token',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function getTokenAttribute(): AccessTokenInterface
    {
        return new AccessToken($this->data);
    }

    public function setTokenAttribute(AccessTokenInterface $token)
    {
        $this->attributes['data'] = $token->jsonSerialize();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('zoho_client.user_class'));
    }

    public static function forUser($user): self
    {
        $user = $user instanceof Model ? $user->getKey() : $user;

        return self::where('user_id', $user)->firstOrFail();
    }
}
