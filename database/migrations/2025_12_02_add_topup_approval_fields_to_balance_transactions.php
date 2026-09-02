<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            // Kolom biaya dan total
            $table->decimal('admin_fee', 15, 2)->default(0)->after('amount');
            $table->decimal('total_payment', 15, 2)->default(0)->after('admin_fee');
            
            // Kolom customer info
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->text('customer_notes')->nullable()->after('customer_email');
            
            // Kolom metode pembayaran
            $table->enum('payment_method', ['qris', 'bank_bca', 'bank_mandiri', 'bank_bni', 'bank_bri', 'other'])->nullable()->after('customer_notes');
            $table->string('proof_of_payment')->nullable()->after('payment_method');
            
            // Kolom approval
            $table->unsignedBigInteger('approved_by')->nullable()->after('proof_of_payment');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            
            // Kolom tracking
            $table->string('request_code')->unique()->nullable()->after('rejection_reason');
            $table->timestamp('expired_at')->nullable()->after('request_code');
            
            // Foreign key
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Update enum status - hati-hati, ini akan mengubah enum existing
        DB::statement("ALTER TABLE balance_transactions MODIFY COLUMN status ENUM('pending', 'waiting_approval', 'approved', 'completed', 'rejected', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_transactions', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            
            $table->dropColumn([
                'admin_fee',
                'total_payment',
                'customer_name',
                'customer_phone',
                'customer_email',
                'customer_notes',
                'payment_method',
                'proof_of_payment',
                'approved_by',
                'approved_at',
                'rejection_reason',
                'request_code',
                'expired_at',
            ]);
        });

        // Kembalikan enum status ke semula
        DB::statement("ALTER TABLE balance_transactions MODIFY COLUMN status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'");
    }
};
