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
        Schema::table('ratings', function (Blueprint $table) {
            // Add rater_id (who gives the rating) and ratee_id (who receives the rating)
            $table->foreignId('rater_id')->after('help_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('ratee_id')->after('rater_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Add type column to differentiate customer->mitra vs mitra->customer ratings
            $table->enum('type', ['customer_to_mitra', 'mitra_to_customer'])->after('ratee_id')->default('customer_to_mitra');
            
            // Add indexes for better query performance
            $table->index(['ratee_id', 'type']);
            $table->index('rater_id');
        });

        // Migrate existing data: user_id = rater (customer), mitra_id = ratee (mitra)
        DB::table('ratings')->update([
            'rater_id' => DB::raw('user_id'),
            'ratee_id' => DB::raw('mitra_id'),
            'type' => 'customer_to_mitra'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex(['ratee_id', 'type']);
            $table->dropIndex(['rater_id']);
            $table->dropColumn(['rater_id', 'ratee_id', 'type']);
        });
    }
};
