<?php

namespace Kiriunin\ApiGuard\Models\Mixins;

use Illuminate\Database\Eloquent\Model;
use Kiriunin\ApiGuard\Models\ApiKey;

trait Apikeyable
{
    public function apiKeys()
    {
        return $this->morphMany(config('apiguard.models.api_key', ApiKey::class), 'apikeyable');
    }

    public function apiKeyOwner()
    {
        return $this->morphOne(config('apiguard.models.api_key', ApiKey::class), 'apiKeyOwner');
    }

    public function createApiKey(Model $owner = null, string $comment = null)
    {
        return ApiKey::make($this, $owner, $comment);
    }
}
