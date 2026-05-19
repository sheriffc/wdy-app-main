<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('district_office', function (Blueprint $table) {
            $table->string('district_code', 10)->nullable()->after('district_id');
        });

        $codes = [
            'Bo'                 => 'BO',
            'Bombali'            => 'BB',
            'Bonthe'             => 'BT',
            'Falaba'             => 'FL',
            'Kailahun'           => 'KL',
            'Kambia'             => 'KB',
            'Karene'             => 'KR',
            'Kenema'             => 'KE',
            'Koinadugu'          => 'KO',
            'Kono'               => 'KN',
            'Moyamba'            => 'MO',
            'Port Loko'          => 'PL',
            'Pujehun'            => 'PJ',
            'Tonkolili'          => 'TK',
            'Western Area Rural' => 'WAR',
            'Western Area Urban' => 'WAU',
        ];

        foreach ($codes as $name => $code) {
            DB::table('district_office')
                ->where('name', $name)
                ->update(['district_code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('district_office', function (Blueprint $table) {
            $table->dropColumn('district_code');
        });
    }
};
