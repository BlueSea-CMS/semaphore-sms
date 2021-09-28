<?php

namespace BlueSea\Semaphore\Models;

use BlueSea\Semaphore\Exceptions\InvalidRequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class User extends Model
{
    protected $table = 'semaphore_accounts';

    protected $fillable = [
        'user_id',
        'email',
        'role',
    ];

    public static function parse($data)
    {
        try {
            return User::updateOrCreate([
                'user_id' => $data['user_id'],
            ], [
                'email' => $data['email'],
                'role' => $data['role'],
            ]);

        } catch (\Exception $e) {
            return User::make($data);
        }
    }
}
