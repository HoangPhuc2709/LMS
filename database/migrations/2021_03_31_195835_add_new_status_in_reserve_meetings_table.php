<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewStatusInReserveMeetingsTable extends Migration
{
    public function up()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            // Sửa enum status
            DB::statement("
                ALTER TABLE `reserve_meetings`
                MODIFY COLUMN `status`
                ENUM('pending','open','finished','canceled')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                NOT NULL
                AFTER `password`
            ");

            // Thêm cột mới (không cần after)
            $table->unsignedInteger('sale_id')->nullable();
            $table->unsignedInteger('date')->after('day');

            // Thêm khóa ngoại
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reserve_meetings', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn(['sale_id', 'date']);
        });
    }
}
