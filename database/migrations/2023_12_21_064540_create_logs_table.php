<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->datetime('timestamp');
            $table->text('ip');
            $table->text('method');
            $table->text('uri');
            $table->enum('module', ['va_inquiry', 'va_payment', 'token_b2b', 'other']);
            $table->string('channel_id')->nullable(true);
            $table->string('partner_id')->nullable(true);
            $table->string('external_id')->nullable(true);
            $table->string('client_id')->nullable(true);
            $table->text('request_header');
            $table->text('request_body');
            $table->text('response');
            $table->integer('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
