<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('company_settings') && !Schema::hasColumn('company_settings', 'company_address')) {
            Schema::table('company_settings', function (Blueprint $table) {
                $table->string('company_address', 500)->nullable()->after('company_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('company_settings', 'company_address')) {
            Schema::table('company_settings', function (Blueprint $table) {
                $table->dropColumn('company_address');
            });
        }
    }
};
