<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartnerCancelFieldsToHelpsTable extends Migration
{
    public function up()
    {
        Schema::table('helps', function (Blueprint $table) {
            // Avoid using ->after() because the referenced column may not exist in all environments.
            $table->timestamp('partner_cancel_requested_at')->nullable();
            $table->text('partner_cancel_reason')->nullable();
            $table->string('partner_cancel_prev_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->dropColumn(['partner_cancel_requested_at', 'partner_cancel_reason', 'partner_cancel_prev_status']);
        });
    }
}
