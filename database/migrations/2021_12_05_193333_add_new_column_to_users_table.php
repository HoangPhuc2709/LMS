<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewColumnToUsersTable extends Migration
{
    public function up()
    {
        // Bước 1: thêm các cột mới (ngoại trừ level_of_training)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->nullable()->after('address');
            $table->unsignedInteger('province_id')->nullable()->after('country_id');
            $table->unsignedInteger('city_id')->nullable()->after('province_id');
            $table->unsignedInteger('district_id')->nullable()->after('city_id');
            $table->point('location')->nullable()->after('district_id');
            $table->boolean('group_meeting')->default(false)->after('location');
        });

        // Bước 2: thêm cột level_of_training (sau khi location đã tồn tại)
        DB::statement("ALTER TABLE `users` ADD COLUMN `level_of_training` bit(3) NULL AFTER `location`");

        // Bước 3: thêm cột meeting_type (vì cần đặt sau level_of_training)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('meeting_type', ['all', 'in_person', 'online'])->default('all')->after('level_of_training');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'country_id',
                'province_id',
                'city_id',
                'district_id',
                'location',
                'group_meeting',
                'level_of_training',
                'meeting_type'
            ]);
        });
    }
}
