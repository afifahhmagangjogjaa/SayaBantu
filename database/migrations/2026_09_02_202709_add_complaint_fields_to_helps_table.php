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
        Schema::table('helps', function (Blueprint $table) {
            if (!Schema::hasColumn('helps', 'complaint_photo')) {
                $table->string('complaint_photo')->nullable()->after('completion_notes');
            }
            if (!Schema::hasColumn('helps', 'complaint_reason')) {
                $table->text('complaint_reason')->nullable()->after('complaint_photo');
            }
            if (!Schema::hasColumn('helps', 'complaint_submitted_at')) {
                $table->timestamp('complaint_submitted_at')->nullable()->after('complaint_reason');
            }
            if (!Schema::hasColumn('helps', 'complaint_resolved_at')) {
                $table->timestamp('complaint_resolved_at')->nullable()->after('complaint_submitted_at');
            }
            if (!Schema::hasColumn('helps', 'complaint_resolution')) {
                $table->string('complaint_resolution')->nullable()->after('complaint_resolved_at'); // 'refunded' | 'rejected'
            }
            if (!Schema::hasColumn('helps', 'complaint_admin_notes')) {
                $table->text('complaint_admin_notes')->nullable()->after('complaint_resolution');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            $columns = [
                'complaint_photo',
                'complaint_reason',
                'complaint_submitted_at',
                'complaint_resolved_at',
                'complaint_resolution',
                'complaint_admin_notes',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('helps', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
