<?php

namespace Avant\LaravelZohoClient;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property int $user_id
 * @property AccessTokenInterface $token
 * @property string $refresh_token
 * @property $user
 */
class ZohoAccessToken extends Model
{
    protected $fillable = [
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function getTokenAttribute(): AccessTokenInterface
    {
        return new AccessToken($this->data);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config(ZohoClientServiceProvider::TAG . '.user_class'));
    }

    public static function forUserId(int $userId): self
    {
        return self::query()->where('user_id', $userId)->firstOrFail();
    }
}
