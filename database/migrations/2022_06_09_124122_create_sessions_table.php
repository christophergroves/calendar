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
    
            $table->id();
            $table->foreignIdFor(Activity::class);
            $table->unsignedBigInteger('parent_id');

            $table->unsignedInteger('session_day');
            $table->time('start_time');
            $table->time('finish_time');
            $table->date('start_date');
            $table->date('finish_date');
            $table->tinyInteger('ends_on');
            $table->tinyInteger('recurrance_type');
            $table->tinyInteger('recurrance_interval');
            $table->tinyInteger('recurrance_monthly_interval');
            $table->unsignedInteger('hours');

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
