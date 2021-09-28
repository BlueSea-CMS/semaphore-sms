
# Semaphore SMS

See Official Semaphore SMS [Documentation here](https://semaphore.co/docs)

#### Installation
* [Composer](#composer)
* [Configuration](#configuration)

#### Usage
* [Sending Message](#sending-message)
* [Sending Priority Message](#sending-priority-message)
* [Sending OTP](#sending-otp)
* [Get Message](#get-message)
* [Get All Messages](#get-all-messages)
* [Get Account Information](#get-account-information)
* [Get All Transactions](#get-all-transactions)
* [Get Sender Names](#get-sender-names)
* [Get All users](#get-all-users)
  
  
  
## Installation
### Composer

Run
```bash
composer require bluesea-cms/semaphore
```

or insert in `require`
``` json
"bluesea-cms/semaphore": "dev-master"
```
### Configuration
Run to publish the Configuration

```bash
php artisan semaphore:publish
```

If you want to keep the data on every execution of the method, you might need to publish all the migrations.

To publish all migrations available
```bash
php artisan semaphore:publish --migration
```


## Usage

Use the Semaphore Facade
```php
use BlueSea\Semaphore\Facades\Semaphore;
```

### Sending Message
To send a message to a single phone number
```php
$messages = Semaphore::send($number, $message);
```

To send a message to a multiple phone number
```php
$messages = Semaphore::send([
  $number1, 
  $number2, 
  $number3,
  ...
], $message);

```
Sending a message, returns an collection of `Message` model.

You can access the the returned message model by:
```php
$message = $messages->first();

$message->message_id;
$message->account;
$message->recipient;
$message->message;
$message->code;
$message->sender_name;
```

### Sending Priority Message

Normally messages are processed in the order they are received and during periods of heavy traffic messaging, messages can be delayed.
With the `Semaphore::priority()` method, it will bypass the default message queue and sends the message immediately.

***This service is 2 credits per 160 character SMS.** 

This method takes the same parameters as the `send` method, and returns the same collection of `Message` model. 
```php
Semaphore::priority($number, $message);
```

### Sending OTP

You can make a custom message included in the OTP sent to the recipient by using the `{otp}` placeholder

For instance using the message: 
**Your One Time Password is: {otp}. Please use it within 5 minutes.**

will return the message 
**Your One Time Password is: XXXXXX. Please use it within 5 minutes.**

***This service is 2 credits per 160 character SMS.**

This method will return a `Message` model instance, and you can then get the `code` by accessing the `code` object from the model 
```php
$otp = Semaphore::otp($number, $message);

$otp->code;
```


### Get Message
To get the Message instance with a specific `message_id`
```php
$message = Semaphore::find($messageId);
```
### Get All Messages
To get all Messages
```php
 $messages = Semaphore::messages();
```

By default, this will only return a maximum of 100 messages per page.
Add a parameter to access the messages on certain pages
```php
 $messages = Semaphore::messages([
	 'page' => 10,
	 'limit' => 500, 				// Default s 100, Maximum of 1000 per page
	 'network' => 'globe', 			// Ex. globe, smart
	 'startDate' => '2020-02-20',	// Format is "YYYY-MM-DD"
	 'endDate', => '2020-12-31',	// Format is "YYYY-MM-DD"
 ]);
```

### Get Account Information

Access your account's information

```php
$account= Semaphore::account();

$account->account_id;

$account->account_name;

$account->status;

$account->credit_balance;
```
### Get All Transactions
Returns a collection of transactions
```php
$transactions = Semaphore::transactions();

$params = [
	 'page' => 10,
	 'limit' => 500, 				// Default s 100, Maximum of 1000 per page
];
$transactions = Semaphore::transactions($params);
 
```

### Get Sender Names
Returns a collection of Sender Names associated with your account
```php
$senderNames = Semaphore::senderNames();

$params = [
	 'page' => 10,
	 'limit' => 500, 				// Default s 100, Maximum of 1000 per page
];

$senderNames = Semaphore::senderNames($params);

```

### Get All users

Returns users associated with your account
```php
$users = Semaphore::users();

$params = [
	 'page' => 10,
	 'limit' => 500, 				// Default s 100, Maximum of 1000 per page
];
$users = Semaphore::users($params);

```
