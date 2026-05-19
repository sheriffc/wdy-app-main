<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->tinyInteger('receives_feeding')->nullable()->after('learning_materials_oids');
        });
    }

    public function down(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->dropColumn('receives_feeding');
        });
    }
};
