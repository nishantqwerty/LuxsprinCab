<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('license_number',50);
            $table->date('expiry_date');
            $table->string('license_front_side');
            $table->string('license_back_side');
            $table->string('car_registeration',50);
            $table->date('car_registeration_expiry_date');
            $table->string('car_registeration_photo');
            $table->date('car_inspection_date');
            $table->string('car_inspection_photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_documents');
    }
}
