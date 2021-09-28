<?php

namespace BlueSea\Semaphore\Models;

use BlueSea\Semaphore\Exceptions\InvalidRequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class SenderName extends Model
{
    protected $table = 'semaphore_sender_names';

    protected $fillable = [
        'name',
        'status',
        'created_at',
    ];

    public static function parse($data)
    {
        if(isset($data['created']))
        {
            $data['created_at'] = $data['created'];
        }
        try {
            return SenderName::updateOrCreate([
                'name' => $data['name'],
            ], [
                'status' => $data['status'],
                'created_at' => $data['created_at']
            ]);

        } catch (\Exception $e) {
            return SenderName::make($data);
        }
    }
}
