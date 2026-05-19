<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->string('council_name', 255)->nullable()->after('chiefdom_id');
        });
    }

    public function down(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->dropColumn('council_name');
        });
    }
};
