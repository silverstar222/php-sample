<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UsersRefreshPasswords extends Model
{
    protected $table = 'users_refresh_passwords';
    protected $primaryKey = 'users_refresh_passwords_id';
    protected $dateFormat = 'U';
    protected $hidden = ['token', 'updated_at', 'created_at'];
    protected $fillable = ['users_refresh_passwords_id', 'users_id'];
    protected $casts = [
        'users_refresh_passwords_id' => 'integer',
        'users_id' => 'integer',
        'token' => 'string',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    /**
     * @param int $usersId
     */
    public function deleteAllUsersTokens(int $usersId):void
    {
        self::where('users_id', '=', $usersId)->delete();
    }

    /**
     * @return string
     */
    public function generateRefreshToken()
    {
        return sha1(str_random(16) . time());
    }

    /**
     * @param string $token
     *
     * @return mixed
     */
    public function getByToken(string $token)
    {
        return self::where('token', '=', $token)->first();
    }
}
