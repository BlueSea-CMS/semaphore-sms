<?php

namespace BlueSea\Semaphore\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'semaphore_transactions';

    protected $fillable = [
        'user_id',
        'user',
        'account_name',
        'status',
        'transaction_method',
        'external_transaction_id',
        'amount',
        'credit_value',
    ];

    public static function parse($data)
    {
        try {
            return Transaction::updateOrCreate([
                'id' => $data['id'],
            ], [
                'user_id' => $data['user_id'],
                'user' => $data['user'],
                'account_name' => $data['account_name'],
                'status' => $data['status'],
                'transaction_method' => $data['transaction_method'],
                'external_transaction_id' => $data['external_transaction_id'],
                'amount' => $data['amount'],
                'credit_value' => $data['credit_value'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
            ]);

        } catch (\Exception $e) {
            return Transaction::make($data);
        }
    }
}
