<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
    
            $table->id()->autoIncrement();
            $table->foreignIdFor(Activity::class);
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->unsignedInteger('session_day');
            $table->time('start_time')->nullable();
            $table->time('finish_time')->nullable();
            $table->date('start_date');
            $table->date('finish_date')->nullable();
            $table->tinyInteger('ends_on');
            $table->tinyInteger('recurrance_type');
            $table->tinyInteger('recurrance_interval')->nullable();
            $table->tinyInteger('recurrance_number')->nullable();
            $table->tinyInteger('recurrance_monthly_interval')->nullable();
            $table->unsignedInteger('hours')->nullable();
            $table->unsignedBigInteger('updated_by');
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
        Schema::dropIfExists('sessions');
    }
}
