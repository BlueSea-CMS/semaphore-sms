<?php

namespace BlueSea\Semaphore\Contracts;

use BlueSea\Semaphore\Exceptions\InvalidRequestException;
use BlueSea\Semaphore\MessageCollection;
use BlueSea\Semaphore\Models\Account;
use BlueSea\Semaphore\Models\Message;
use BlueSea\Semaphore\Models\SenderName;
use BlueSea\Semaphore\Models\Transaction;
use BlueSea\Semaphore\Models\User;
use BlueSea\Semaphore\SenderNameCollection;
use BlueSea\Semaphore\TransactionCollection;
use BlueSea\Semaphore\UserCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class HttpConnector
{
    protected $apikey;

    protected $senderName;

    protected $response;

    protected $data;

    private $error_keys = [
        'apikey',
        'number',
        'sendername',
    ];

    const MESSAGE = 'https://api.semaphore.co/api/v4/messages';
    /**
     * To send any outgoing SMS message, send an HTTP POST request to:
     * https://api.semaphore.co/api/v4/messages
     *
     * Note: This endpoint is limited to being called 120 times per minute
     *
     *
     * To send multiple messages to the different number, you can specify up to 1,000 numbers (separated by commas) in recipient field.
     * This is the preferred way to send "bulk" messages as it allows you to avoid API rate limits.
     *
     * cURL Exmaple
     * curl --data "apikey=YOUR_API_KEY&number=NUMBER1,NUMBER2,NUMBER3&message=I just sent my first bulk message with Semaphore" https://semaphore.co/api/v4/messages
     *
     *
     *
     * @param string $apikey
     * @param string|array $number
     * @param string $message
     * @param string $senderName
     *
     *
     * @return
     * [
     *      'message_id',   - The unique identifier for your message
     *      'user_id',      - The unique identifier for the user who sent the message
     *      'user',         - The email address of the user who sent the message
     *      'account_id',   - The unique identifier of the account the message was sent by
     *      'account',      - The name of the account the message was sent by
     *      'recipient',    - The phone number the message was sent to
     *      'message',      - The body of the message that was sent
     *      'sender_name',  - The Sender Name the message was sent from
     *      'network',      - The recipient phone number's network
     *      'status',       - queued | pending | sent | failed | refunded
     *                          * Queued    -   The message is queued to be sent to the network
     *                          * Pending   -   The message is in transit to the network
     *                          * Sent      -   The message was successfully delivered to the network
     *                          * Failed 	-   The message was rejected by the network and is waiting to be refunded
     *                          * Refunded 	-   The message has been refunded and your account balance has been adjusted
     *      'type',         - If the message was sent to one number the type will be "single". If the message was sent to more
     *                          than "one" number the type will be "bulk". If the message was sent to the priority queue the type will be "priority"
     *      'source',       - The source of the message. For messages sent via API, the source will be "api". For messages sent through the web tool,
     *                          the source will be "webtool". For messages sent through the bulk tool, the source will be "csv"
     *      'created_at',   - The timestamp the message was created
     *      'updated_at',   - The timestamp the message was last updated
     * ];
     *
     *
     *
     * To retrieve outgoing SMS messages, send an HTTP GET request to:
     *
     * https://api.semaphore.co/api/v4/messages
     *
     * Note: This endpoint is limited to being called 30 times per minute
     *
     *
     * @param string $apikey            - Required
     * @param int $limit                - Optional (Default 100, Max 1000)
     *                                  - Defines the number of messages to retrieve per request. The default is 100. Max is 1000.
     * @param int $page                 - Optional (Default 1)
     *                                  - Specifies which page of the results to return. The default is 1.
     * @param int $startDate            - Optional (Default 1)
     *                                  - Limits messages to the specified period defined by startDate and endDate. Format is "YYYY-MM-DD"
     * @param int $endDate              - Optional (Default 1)
     *                                  - Limits messages to the specified period defined by startDate and endDate. Format is "YYYY-MM-DD"
     * @param string $network           - Optional
     *                                  - Return only messages sent to the specified network. Format is lowercase (e.g. "globe", "smart")
     * @param string $status            - Optional
     *                                  - Return only messages with the specified status. Format is lowercase (e.g. "pending", "success")
     *
     *
     * @return
     * [
     *      'message_id',   - The unique identifier for your message
     *      'user_id',      - The unique identifier for the user who sent the message
     *      'user',         - The email address of the user who sent the message
     *      'account_id',   - The unique identifier of the account the message was sent by
     *      'account',      - The name of the account the message was sent by
     *      'recipient',    - The phone number the message was sent to
     *      'message',      - The body of the message that was sent
     *      'sender_name',  - The Sender Name the message was sent from
     *      'network',      - The recipient phone number's network
     *      'status',       - queued | pending | sent | failed | refunded
     *                          * Queued    -   The message is queued to be sent to the network
     *                          * Pending   -   The message is in transit to the network
     *                          * Sent      -   The message was successfully delivered to the network
     *                          * Failed 	-   The message was rejected by the network and is waiting to be refunded
     *                          * Refunded 	-   The message has been refunded and your account balance has been adjusted
     *      'type',         - If the message was sent to one number the type will be "single". If the message was sent to more
     *                          than "one" number the type will be "bulk". If the message was sent to the priority queue the type will be "priority"
     *      'source',       - The source of the message. For messages sent via API, the source will be "api". For messages sent through the web tool,
     *                          the source will be "webtool". For messages sent through the bulk tool, the source will be "csv"
     *      'created_at',   - The timestamp the message was created
     *      'updated_at',   - The timestamp the message was last updated
     * ];
     *
     * To retrieve a single outgoing SMS message by its unique id, send an HTTP GET request to:
     *
     * https://api.semaphore.co/api/v4/messages/{id}
     *
     * The format of the single message that matches the id provided is the same received when sending messages.
     *
     * */


    const PRIORITY = 'https://api.semaphore.co/api/v4/priority';
    /**
     * Normally messages are processed in the order they are received and during periods of heavy traffic messaging, messages can be delayed.
     * If your message is time sensitive, you may wish to use our premium priority queue which bypasses the default message queue and sends the
     * message immediately. This service is 2 credits per 160 character SMS.
     *
     * Note: This endpoint is not rate limited
     *
     *
     * @param string $apikey
     * @param string|array $number
     * @param string $message
     * @param string $senderName
     *
     *
     * @return
     * [
     *      'message_id',   - The unique identifier for your message
     *      'user_id',      - The unique identifier for the user who sent the message
     *      'user',         - The email address of the user who sent the message
     *      'account_id',   - The unique identifier of the account the message was sent by
     *      'account',      - The name of the account the message was sent by
     *      'recipient',    - The phone number the message was sent to
     *      'message',      - The body of the message that was sent
     *      'sender_name',  - The Sender Name the message was sent from
     *      'network',      - The recipient phone number's network
     *      'status',       - Queued | Pending | Sent | Failed | Refunded
     *                          * Queued    -   The message is queued to be sent to the network
     *                          * Pending   -   The message is in transit to the network
     *                          * Sent      -   The message was successfully delivered to the network
     *                          * Failed 	-   The message was rejected by the network and is waiting to be refunded
     *                          * Refunded 	-   The message has been refunded and your account balance has been adjusted
     *      'type',         - If the message was sent to one number the type will be "single". If the message was sent to more
     *                          than "one" number the type will be "bulk". If the message was sent to the priority queue the type will be "priority"
     *      'source',       - The source of the message. For messages sent via API, the source will be "api". For messages sent through the web tool,
     *                          the source will be "webtool". For messages sent through the bulk tool, the source will be "csv"
     *      'created_at',   - The timestamp the message was created
     *      'updated_at',   - The timestamp the message was last updated
     * ];
     *
     */

    const OTP = 'https://api.semaphore.co/api/v4/otp';
    /**
     * Semaphore also provides a simple and easy interface for generating OTP on the fly. This service is 2 credits per 160 character SMS.
     *
     * Note: This endpoint is not rate limited
     *
     *
     * This endpoint accepts the exact same payload as a regular message but you can specify where in the message
     * to insert the OTP code by using the placeholder "{otp}"
     *
     * For instance using the message:
     *
     * "Your One Time Password is: {otp}. Please use it within 5 minutes."
     *
     * will return the message
     *
     * "Your One Time Password is: XXXXXX. Please use it within 5 minutes."
     *
     *
     * If you do not provide the placeholder, the OTP code will be appended to your original message.
     * For instance if you send the message
     *
     * "Thanks for registering"
     *
     * the message will have the OTP appended to the end as
     *
     * "Thanks for registering. Your One Time Password is XXXXXX"
     *
     *
     *
     * @param string $apikey
     * @param string|array $number
     * @param string $message
     * @param string $senderName
     *
     *
     * @return
     * [
     *      'message_id',   - The unique identifier for your message
     *      'user_id',      - The unique identifier for the user who sent the message
     *      'user',         - The email address of the user who sent the message
     *      'account_id',   - The unique identifier of the account the message was sent by
     *      'account',      - The name of the account the message was sent by
     *      'recipient',    - The phone number the message was sent to
     *      'message',      - The body of the message that was sent
     *      'code',         - Generated OTP Code
     *      'sender_name',  - The Sender Name the message was sent from
     *      'network',      - The recipient phone number's network
     *      'status',       - Queued | Pending | Sent | Failed | Refunded
     *                          * Queued    -   The message is queued to be sent to the network
     *                          * Pending   -   The message is in transit to the network
     *                          * Sent      -   The message was successfully delivered to the network
     *                          * Failed 	-   The message was rejected by the network and is waiting to be refunded
     *                          * Refunded 	-   The message has been refunded and your account balance has been adjusted
     *      'type',         - If the message was sent to one number the type will be "single". If the message was sent to more
     *                          than "one" number the type will be "bulk". If the message was sent to the priority queue the type will be "priority"
     *      'source',       - The source of the message. For messages sent via API, the source will be "api". For messages sent through the web tool,
     *                          the source will be "webtool". For messages sent through the bulk tool, the source will be "csv"
     *      'created_at',   - The timestamp the message was created
     *      'updated_at',   - The timestamp the message was last updated
     * ];
     *
     *
     */


    const ACCOUNT = 'https://api.semaphore.co/api/v4/account';
    /**
     *
     * To retrieve basic information about your account, send an HTTP GET request to:
     *
     * https://api.semaphore.co/api/v4/account
     *
     * Note: This endpoint is limited to being called 2 times per minute
     *
     * @param string $apiKey
     *
     * This call returns a JSON response containing the following parameters for the account:
     *
     * @return
     * [
     *      'account_id',       - The unique identifier for your account
     *      'account_name',     - The company name listed on your account
     *      'status',           - The status of your account
     *      'credit_balance',   - The credit balance of your account. Each credit equals one SMS.
     * ];
     *
     */

    const TRANSACTIONS = 'https://api.semaphore.co/api/v4/account/transactions';
    /**
     * To retrieve transaction information about your account, send an HTTP GET request to:
     *
     * https://api.semaphore.co/api/v4/account/transactions
     *
     * Note: This endpoint is limited to being called 2 times per minute
     *
     * @param string $apikey        - Required
     * @param int $limit            - Defines the number of transactions to retrieve per request. The default is 100. Max is 1000.
     * @param int $page 	        - Specifies which page of the results to return. The default is 1.

     * This call returns a JSON response containing the following parameters for the account:
     *
     * @return array [
     *  'account_id'        - 	The unique identifier for your account
     *  'account_name'      - 	The company name listed on your account
     *  'status'            - 	The status of your account
     *  'credit_balance'    - 	The credit balance of your account. Each credit equals one SMS.
     *
     * ]


     */

    const SENDERNAMES = 'https://api.semaphore.co/api/v4/account/sendernames';
    /**
     * To retrieve Sender Names associated with your account, send an HTTP GET request to:
     *
     *
     * https://api.semaphore.co/api/v4/account/sendernames
     *
     * Note: This endpoint is limited to being called 2 times per minute
     *
     * @param string $apikey        - Required
     * @param int $limit            - Defines the number of transactions to retrieve per request. The default is 100. Max is 1000.
     * @param int $page 	        - Specifies which page of the results to return. The default is 1.
     *
     * This call returns a JSON response containing the following parameters for the account:
     *
     *
     * @return array [
     *  'name'              - 	The sender name value
     *  'status'            - 	The status of the sender name
     *  'created_at'        - 	The date the sender name was created
     *
     * ]
     *
     *
     */

    const USERS = 'https://api.semaphore.co/api/v4/account/users';
    /**
     * To retrieve users associated with your account, send an HTTP GET request to:
     *
     * https://api.semaphore.co/api/v4/account/users
     *
     * @param string $apikey        - Required
     * @param int $limit            - Defines the number of transactions to retrieve per request. The default is 100. Max is 1000.
     * @param int $page 	        - Specifies which page of the results to return. The default is 1.
     *
     * This call returns a JSON response containing the following parameters for the account:
     *
     * @return array [
     *  'email'             - 	The email address for the user
     *  'role'              - 	The user's role on your account
     *  'status'            - 	The status of the user
     *
     * ]
     *
     */

    public function __construct($config = [])
    {
        $this->apikey = isset($config['api_key']) ? $config['api_key'] : null;

        $this->senderName = isset($config['sender_name']) ? $config['sender_name'] : null;

    }

    private function formatNumber($number)
    {
        if(!is_array($number))
        {
            return $number;
        }

        return implode(',', $number);
    }

    private function parseMessage()
    {
        if(is_array($this->data))
        {
            $messages = new MessageCollection();

            foreach($this->data as $message)
            {
                $messages->add(Message::parse((array) $message));
            }

            return $messages;
        }

        return null;
    }

    private function parseTransactions()
    {
        if(is_array($this->data))
        {
            $transactions = new TransactionCollection();

            foreach($this->data as $transaction)
            {
                $transactions->add(Transaction::parse((array) $transaction));
            }

            return $transactions;
        }

        return null;
    }

    private function parseSenderNames()
    {
        if(is_array($this->data))
        {
            $names = new SenderNameCollection();

            foreach($this->data as $name)
            {
                $names->add(SenderName::parse((array) $name));
            }

            return $names;
        }

        return null;
    }

    private function parseUsers()
    {
        if(is_array($this->data))
        {
            $users = new UserCollection();

            foreach($this->data as $user)
            {
                $users->add(User::parse((array) $user));
            }

            return $users;
        }

        return null;
    }

    private function parseAccount()
    {
        return Account::parse((array) $this->data);
    }

    private function config()
    {
        return [
            'apikey' => $this->apikey,
            'sendername' => $this->senderName,
        ];
    }

    private function validateResponse()
    {
        if(!is_array($this->data))
        {
            $keys = array_keys((array) $this->data);

            foreach($keys as $key)
            {
                if(array_search($key, $this->error_keys) > -1 && is_array($key))
                {
                    throw new InvalidRequestException(implode(', ', $this->data->$key[0]), Response::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function post($url, $data = [])
    {
        $data = array_merge($data, $this->config());

        $this->response = Http::post($url, $data);

        $this->data = json_decode($this->response->body());

        return $this->response;
    }

    /**
     * @param string $url
     * @param array|string|null $query
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function get($url, $query = [])
    {
        $query = array_merge($query, $this->config());

        $this->response = Http::get($url, $query);

        $this->data = json_decode($this->response->body());

        return $this->response;
    }


    /**
     * @param string $apikey
     * @param string|array $number
     * @param string $message
     * @param string $senderName
     *
     * @return \BlueSea\Semaphore\MessageCollection
     */
    public function send($number, $message)
    {
        if(!is_array($number))
        {
            $number = [$number];
        }

        $this->post(static::MESSAGE, [
            'number' => $this->formatNumber($number),
            'message' => $message,
        ]);

        $this->validateResponse();

        return $this->parseMessage();
    }

    /**
     * @param string $apikey
     * @param string $number
     * @param string $message
     * @param string $senderName
     *
     * @return \BlueSea\Semaphore\MessageCollection
     */
    public function otp($number, $message)
    {
        $this->post(static::OTP, [
            'number' => $number,
            'message' => $message,
        ]);

        $this->validateResponse();

        return $this->parseMessage();
    }

    /**
     * @param string $apikey
     * @param string|array $number
     * @param string $message
     * @param string $senderName
     *
     * @return \BlueSea\Semaphore\MessageCollection
     */
    public function priority($number, $message)
    {
        if(!is_array($number))
        {
            $number = [$number];
        }

        $this->post(static::PRIORITY, [
            'number' => $this->formatNumber($number),
            'message' => $message,
        ]);

        $this->validateResponse();

        return $this->parseMessage();
    }

    /**
     *
     * @param int|string $messageId
     *
     * @return \BlueSea\Semaphore\Models\Message
     */
    public function find($messageId)
    {
        $this->get(static::MESSAGE . '/' . $messageId);

        $this->validateResponse();

        return $this->parseMessage()->first();
    }

    /**
     *
     * @return \BlueSea\Semaphore\MessageCollection
     */
    public function messages()
    {
        $this->get(static::MESSAGE);

        $this->validateResponse();

        return $this->parseMessage();
    }

    /**
     *
     * @return \BlueSea\Semaphore\Models\Account
     */

    public function account()
    {
        $this->get(static::ACCOUNT);

        $this->validateResponse();

        return $this->parseAccount();
    }

    /**
     * @param array|null $query
     */
    public function transactions($query = [])
    {
        $this->get(static::TRANSACTIONS, $query);

        $this->validateResponse();

        return $this->parseTransactions();
    }

    /**
     * @param array|null $query
     */
    public function senderNames($query = [])
    {
        $this->get(static::SENDERNAMES, $query);

        $this->validateResponse();

        return $this->parseSenderNames();
    }

    /**
     * @param array|null $query
     */
    public function users($query = [])
    {
        $this->get(static::USERS, $query);

        $this->validateResponse();

        return $this->parseUsers();
    }

}
