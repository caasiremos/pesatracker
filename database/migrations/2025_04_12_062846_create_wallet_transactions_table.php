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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->index()->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->index()->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->string('transaction_phone_number');
            $table->string('provider')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('external_reference')->nullable();
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
        Schema::dropIfExists('wallet_transactions');
    }
};
