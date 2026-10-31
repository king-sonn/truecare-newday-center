<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->unsignedInteger('amount');
            $table->string('business_no')->default('247247');
            $table->string('account_no')->default('958526');
            $table->string('account_name')->default('Truecare new day center');
            $table->string('checkout_request_id')->nullable()->unique();
            $table->string('merchant_request_id')->nullable();
            $table->string('mpesa_receipt')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('result_code')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('checkout_request_id');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};