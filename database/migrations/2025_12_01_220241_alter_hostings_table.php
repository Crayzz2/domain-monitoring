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
        Schema::table('hostings', function (Blueprint $table) {
            $table->dropColumn('is_third_party');
            $table->dropColumn('hosting_providers_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->boolean('is_third_party')->default(0)->after('expiration_date');
            $table->unsignedBigInteger('hosting_providers_id')->nullable()->after('is_third_party');
        });
    }
};
