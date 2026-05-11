<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user')) {
            Schema::table('user', function (Blueprint $table) {
//            $table->renameColumn('user_role', 'to');
                //add these new cols
                $table->after('remember_token', function ($table) {
                    $table->string('district_office_id', 100)->nullable(); // at registration
                    $table->string('requested_role', 100)->nullable(); // at registration
                    $table->string('granted_role', 100)->nullable(); // on approval
                });
                //remove the role
                $table->dropColumn('user_role');
                //allow email to be nullable
                $table->string('email')->nullable()->change();
                //phone not nullable
                $table->string('phone', 30)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('user')) {
            Schema::table('user', function (Blueprint $table) {
                //remove these new cols
                $table->dropColumn('district_office_id');
                $table->dropColumn('requested_role'); // at registration
                $table->dropColumn('granted_role'); // on approval
                //add the role
                $table->after('remember_token', function ($table) {
                    $table->string('user_role', 100)->nullable();
                });
                //reverse changes
                $table->string('email')->unique()->change();
                $table->string('phone', 30)->nullable()->change();

            });
        }
    }
};
