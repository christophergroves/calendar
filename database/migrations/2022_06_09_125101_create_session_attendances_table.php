<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_attendances', function (Blueprint $table) {
            $table->id()->autoIncrement();;
            $table->unsignedBigInteger('session_id');
            $table->tinyInteger('absence');
            $table->date('absense_date');
            $table->tinyInteger('session_deleted');
            $table->string('attendance_notes');
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('session_attendances');
    }
}
