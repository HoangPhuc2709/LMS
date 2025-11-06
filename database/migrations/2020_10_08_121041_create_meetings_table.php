<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('teacher_id'); //  Thêm dòng này
            $table->unsignedInteger('amount')->nullable();
            $table->integer('discount')->nullable();
            $table->boolean('disabled')->default(0);
            $table->integer('created_at');

            // 🔗 Foreign keys
            $table->foreign('creator_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('teacher_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
