<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'users_id';
    protected $dateFormat = 'U';
    protected $hidden = ['updated_at'];
    protected $fillable = ['users_id', 'user_name', 'email', 'password', 'created_at'];
    protected $casts = [
        'users_id' => 'integer',
        'user_name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'created_at' => 'integer',
        'updated_at' => 'integer'
    ];

    /**
     * @param array $array
     *
     * @return mixed
     */
    public function createNew(array $array)
    {
        return self::create($array);
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return self::where('email', '=', $email)->first();
    }
}
