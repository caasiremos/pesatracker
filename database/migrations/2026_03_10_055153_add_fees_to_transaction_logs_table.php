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
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->string('internal_transaction_id')->nullable();
            $table->decimal('fee', 19, 4)->default(0);
            $table->decimal('provider_fee', 19, 4)->default(0);
            $table->string('external_status')->nullable();
            $table->text('error_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropColumn('internal_transaction_id');
            $table->dropColumn('fee');
            $table->dropColumn('provider_fee');
            $table->dropColumn('external_status');
            $table->dropColumn('error_message');
        });
    }
};
