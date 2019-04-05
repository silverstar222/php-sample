<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UsersRefreshTokens extends Model
{
    protected $table = 'users_refresh_tokens';
    protected $primaryKey = 'users_refresh_tokens_id';
    protected $dateFormat = 'U';
    protected $hidden = ['token', 'updated_at', 'device_token'];
    protected $fillable = ['users_refresh_tokens_id', 'users_id', 'ip','user_agent', 'created_at'];
    protected $casts = [
        'users_refresh_tokens_id' => 'integer',
        'users_id' => 'integer',
        'ip' => 'string',
        'user_agent' => 'string',
        'token' => 'string',
        'created_at' => 'integer',
        'updated_at' => 'integer',
        'device_token' => 'string'
    ];

    /**
     * @return string
     */
    public function generateRefreshToken()
    {
        return Hash::make(str_random(16) . time());
    }

    /**
     * @param string $refreshToken
     *
     * @return mixed
     */
    public function getByRefreshToken(string  $refreshToken)
    {
        return self::where('token', '=', $refreshToken)->first();
    }

    /**
     * @param string $refreshToken
     *
     * @return mixed
     */
    public function deleteByRefreshToken(string  $refreshToken)
    {
        return self::where('token', '=', $refreshToken)->delete();
    }

    /**
     * @param string $deviceToken
     *
     * @return bool
     */
    public function checkExistsDeviceToken(string $deviceToken)
    {
        $item = self::where('device_token', '=', $deviceToken)->first();
        if (!empty($item))
        {
            return $item;
        }

        return false;
    }


}
