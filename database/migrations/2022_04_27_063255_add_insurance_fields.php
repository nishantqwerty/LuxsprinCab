<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsuranceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_documents',function(Blueprint $table){
            $table->string('insurance_number')->after('license_back_side');
            $table->date('insurance_expiry_date')->after('insurance_number');
            $table->string('insurance_image')->after('insurance_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_documents',function(Blueprint $table){
            $table->dropColumn('insurance_number');
            $table->dropColumn('insurance_expiry_date');
            $table->dropColumn('insurance_image');
        });
    }
}
