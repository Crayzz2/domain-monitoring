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
            $table->boolean('send_alerts');
            $table->text('internal_alert_message_level_one')->nullable();
            $table->text('internal_alert_message_level_two')->nullable();
            $table->text('internal_alert_message_level_three')->nullable();
            $table->text('internal_alert_message_level_four')->nullable();
            $table->text('client_alert_message_level_one')->nullable();
            $table->text('client_alert_message_level_two')->nullable();
            $table->text('client_alert_message_level_three')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('send_alerts');
            $table->dropColumn('internal_alert_message_level_one');
            $table->dropColumn('internal_alert_message_level_two');
            $table->dropColumn('internal_alert_message_level_three');
            $table->dropColumn('internal_alert_message_level_four');
            $table->dropColumn('client_alert_message_level_one');
            $table->dropColumn('client_alert_message_level_two');
            $table->dropColumn('client_alert_message_level_three');
        });
    }
};
