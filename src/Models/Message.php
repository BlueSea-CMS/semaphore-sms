<?php

namespace BlueSea\Semaphore\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'semaphore_messages';

    protected $fillable = [
        'message_id',
        'user_id',
        'user',
        'account_id',
        'account',
        'recipient',
        'message',
        'code',
        'sender_name',
        'network',
        'status',
        'type',
        'source',
    ];

    public static function parse($data)
    {
        try {
            return Message::create($data);

        } catch (\Exception $e) {
            return Message::make($data);
        }
    }
}
