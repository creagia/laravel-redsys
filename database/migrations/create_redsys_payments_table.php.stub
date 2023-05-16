<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('redsys_requests', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('model', 'model_morph');
            $table->boolean('save_card')->default(false);
            $table->nullableMorphs('card_request_model', 'card_model_morph');
            $table->uuid('uuid');
            $table->enum('status', ['pending', 'denied', 'paid'])->default('pending');
            $table->bigInteger('order_number')->unique();
            $table->bigInteger('amount');
            $table->integer('currency');
            $table->string('pay_method');
            $table->string('transaction_type');
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();
            $table->string('auth_code')->nullable();
            $table->timestamps();
        });

        Schema::create('redsys_cards', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('model');
            $table->uuid('uuid');
            $table->string('number');
            $table->integer('expiration_date');
            $table->string('merchant_identifier');
            $table->string('cof_transaction_id')->nullable();
            $table->timestamps();
        });

         Schema::create('redsys_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('redsys_request_id')->nullable();
            $table->json('merchant_parameters');
            $table->timestamps();
         });
    }
};
