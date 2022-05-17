<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatLongToBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('lat1')->after('is_scheduled');
            $table->string('long1')->after('lat1');
            $table->string('lat2')->after('long1');
            $table->string('long2')->after('lat2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('lat1');
            $table->dropColumn('long1');
            $table->dropColumn('lat2');
            $table->dropColumn('long2');
        });
    }
}
