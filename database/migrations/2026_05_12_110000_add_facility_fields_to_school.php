<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->string('classrooms_oid', 30)->nullable()->after('address');
            $table->string('wash_oids', 100)->nullable()->after('classrooms_oid');
            $table->string('electricity_oids', 100)->nullable()->after('wash_oids');
            $table->string('mno_oids', 100)->nullable()->after('electricity_oids');
            $table->string('learning_materials_oids', 100)->nullable()->after('mno_oids');
        });
    }

    public function down(): void
    {
        Schema::table('school', function (Blueprint $table) {
            $table->dropColumn(['classrooms_oid', 'wash_oids', 'electricity_oids', 'mno_oids', 'learning_materials_oids']);
        });
    }
};
