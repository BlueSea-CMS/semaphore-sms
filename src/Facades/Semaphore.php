<?php

namespace BlueSea\Semaphore\Facades;

use BlueSea\Semaphore\Contracts\Semaphore as ContractsSemaphore;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \BlueSea\Semaphore\Models\Message send(string|array $number, $string $message)
 * @method static \BlueSea\Semaphore\Models\Message priority(string|array $number, $string $message)
 * @method static \BlueSea\Semaphore\Models\Message otp(string $number, $string $message)
 * @method static \BlueSea\Semaphore\Models\Message find(int|string $messageId)
 * @method static \BlueSea\Semaphore\MessageCollection messages()
 * @method static \BlueSea\Semaphore\Models\Account account()
 * @method static \BlueSea\Semaphore\TransactionCollection transactions(array|null $query)
 * @method static \BlueSea\Semaphore\SenderNameCollection senderNames(array|null $query)
 * @method static \BlueSea\Semaphore\UserCollection users(array|null $query)
 */
class Semaphore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new ContractsSemaphore;
    }
}
