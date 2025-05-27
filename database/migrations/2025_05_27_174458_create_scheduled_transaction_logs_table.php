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
        Schema::create('scheduled_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_transaction_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->integer('amount');
            $table->decimal('fee', 10, 2)->default(0);
            $table->date('scheduled_date');
            $table->string('internal_transaction_reference')->nullable();
            $table->string('external_transaction_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_transaction_logs');
    }
};
