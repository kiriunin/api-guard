<?php

namespace Kiriunin\ApiGuard\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Request;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'comment',
        'apikeyable_id',
        'apikeyable_type',
        'last_ip_address',
        'last_used_at',
        'owner_id',
        'owner_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function apikeyable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function apiKeyOwner()
    {
        return $this->morphTo();
    }

    /**
     * @param Model $apikeyable
     * @param Model|null $owner
     * @param string|null $comment
     * @return ApiKey
     */
    public static function make(Model $apikeyable, Model $owner = null, string $comment = null)
    {
        $apiKey = new ApiKey([
            'key'             => self::generateKey(),
            'apikeyable_id'   => $apikeyable->id,
            'apikeyable_type' => get_class($apikeyable),
            'last_ip_address' => Request::ip(),
            'last_used_at'    => Carbon::now(),
            'comment'         => $comment,
            'owner_id'   => $owner->id,
            'owner_type' => get_class($owner),
        ]);

        $apiKey->save();

        return $apiKey;
    }

    /**
     * A sure method to generate a unique API key
     *
     * @return string
     */
    public static function generateKey()
    {
        do {
            $salt = sha1(time() . mt_rand());
            $newKey = substr($salt, 0, 40);
        } // Already in the DB? Fail. Try again
        while (self::keyExists($newKey));

        return $newKey;
    }

    /**
     * Checks whether a key exists in the database or not
     *
     * @param $key
     * @return bool
     */
    private static function keyExists($key)
    {
        $apiKeyCount = self::where('key', '=', $key)->limit(1)->count();

        if ($apiKeyCount > 0) return true;

        return false;
    }
}
