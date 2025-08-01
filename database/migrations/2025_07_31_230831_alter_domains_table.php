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
        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('is_third_party')->default(0)->after('client_id');
            $table->string('host_user')->nullable()->after('is_third_party');
            $table->string('host_password')->nullable()->after('host_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn('is_third_party');
            $table->dropColumn('host_user');
            $table->dropColumn('host_password');
        });
    }
};
