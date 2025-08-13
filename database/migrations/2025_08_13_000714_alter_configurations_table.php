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
            $table->dropColumn('whatsapp_message');
            $table->text('domain_default_message')->nullable()->after('notification_receive_email');
            $table->text('hosting_default_message')->nullable()->after('domain_default_message');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('whatsapp_message')->nullable()->after('notification_receive_email');
            $table->dropColumn('domain_default_message');
            $table->dropColumn('hosting_default_message');
        });
    }
};
