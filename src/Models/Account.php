<?php

namespace BlueSea\Semaphore\Models;

use BlueSea\Semaphore\Exceptions\InvalidRequestException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Account extends Model
{
    protected $table = 'semaphore_accounts';

    protected $fillable = [
        'account_id',
        'account_name',
        'status',
        'credit_balance',
    ];

    public static function parse($data)
    {
        if(!isset($data['account_id']) || !isset($data['account_name']) || !isset($data['status']) || !isset($data['credit_balance']))
        {
            throw new InvalidRequestException('Account Not Found', Response::HTTP_NOT_FOUND);
        }

        try {
            return Account::updateOrCreate([
                'account_id' => $data['account_id'],
            ], [
                'account_name' => $data['account_name'],
                'status' => $data['status'],
                'credit_balance' => $data['credit_balance']
            ]);

        } catch (\Exception $e) {
            return Account::make($data);
        }
    }
}
