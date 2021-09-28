<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemaphoreMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semaphore_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('user_id');
            $table->string('user');
            $table->string('account_id');
            $table->string('account')->nullable();
            $table->text('recipient');
            $table->text('message');
            $table->string('code')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('network')->nullable();
            $table->string('status');
            $table->string('type');
            $table->string('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semaphore_messages');
    }
}
