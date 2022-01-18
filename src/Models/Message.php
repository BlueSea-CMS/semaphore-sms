<?php

namespace BlueSea\Semaphore\Models;

use BlueSea\Semaphore\Facades\Semaphore;
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
            if(isset($data['id'])) {
                
                $tmp = Message::find($data['id']);

                if($tmp != null) {
                    return $tmp->update($data);
                }
            }
             
            return Message::create($data);

        } catch (\Exception $e) {
            return Message::make($data);
        }
    }

    public static function booted()
    {
        static::retrieved(function($data) {
            if($data->status == 'Pending') {
                $data = Semaphore::find($data->message_id);
            }
        });
    }
}
