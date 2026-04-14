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
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('send_alerts');
            $table->boolean('send_internal_alerts')->after('instance_status');
            $table->boolean('send_client_alerts')->after('send_internal_alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('send_alerts')->after('instance_status');
            $table->dropColumn('send_internal_alerts');
            $table->dropColumn('send_client_alerts');
        });
    }
};
