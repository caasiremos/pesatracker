<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addColumns = ! Schema::hasColumn('scheduled_transaction_logs', 'customer_id');

        if ($addColumns) {
            Schema::table('scheduled_transaction_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('scheduled_transaction_id');
                $table->string('telco_provider')->nullable();
                $table->decimal('provider_fee', 19, 4)->default(0);
                $table->string('external_status')->nullable();
                $table->text('error_message')->nullable();
            });
        } else {
            // Column already exists (e.g. from a previous run); ensure it is nullable so we can clean orphans
            DB::statement('ALTER TABLE scheduled_transaction_logs MODIFY customer_id BIGINT UNSIGNED NULL');
        }

        // Null out any customer_id that don't exist in customers (orphaned refs)
        $validCustomerIds = DB::table('customers')->pluck('id');
        $query = DB::table('scheduled_transaction_logs')->whereNotNull('customer_id');
        if ($validCustomerIds->isNotEmpty()) {
            $query->whereNotIn('customer_id', $validCustomerIds);
        }
        $query->update(['customer_id' => null]);

        // Add FK only if not already present (e.g. previous run added column but FK failed)
        $driver = Schema::getConnection()->getDriverName();
        $tableName = 'scheduled_transaction_logs';
        $hasFk = false;
        if ($driver === 'mysql') {
            $constraint = DB::selectOne(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                 AND CONSTRAINT_NAME LIKE '%customer_id%'",
                [$tableName]
            );
            $hasFk = $constraint !== null;
        }

        if (! $hasFk) {
            Schema::table('scheduled_transaction_logs', function (Blueprint $table) {
                $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_transaction_logs', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('scheduled_transaction_logs', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'telco_provider', 'provider_fee', 'external_status', 'error_message']);
        });
    }
};
