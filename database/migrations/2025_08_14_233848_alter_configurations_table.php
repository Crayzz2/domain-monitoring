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
            $table->string('default_color')->nullable()->after('hosting_default_message');
            $table->string('domain_default_filter_days')->nullable()->after('default_color');
            $table->string('hosting_default_filter_days')->nullable()->after('domain_default_filter_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('default_color');
            $table->dropColumn('domain_default_filter_days');
            $table->dropColumn('hosting_default_filter_days');
        });
    }
};
