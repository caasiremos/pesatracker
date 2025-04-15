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
        Schema::create('scheduled_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->index()->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->dateTime('payment_date');
            $table->string('frequency');
            $table->string('reference');
            $table->string('note')->nullable();
            $table->string('provider');
            $table->string('transaction_phone_number');
            $table->string('transaction_reference')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('telecom_product')->nullable();
             $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_transactions');
    }
};
