<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeMeetingIdToMeetingTimeIdInAccountingTable extends Migration
{
    public function up()
    {
        Schema::table('accounting', function (Blueprint $table) {
            // Nếu có khóa ngoại thì mới xóa
            try {
                DB::statement("ALTER TABLE `accounting` DROP FOREIGN KEY `accounting_meeting_id_foreign`;");
            } catch (\Exception $e) {
                // Bỏ qua nếu không tồn tại
            }

            // Nếu cột meeting_id tồn tại thì đổi tên
            if (Schema::hasColumn('accounting', 'meeting_id')) {
                DB::statement("ALTER TABLE `accounting` CHANGE COLUMN `meeting_id` `meeting_time_id` INTEGER UNSIGNED NULL;");
            }
        });
    }

    public function down()
    {
        Schema::table('accounting', function (Blueprint $table) {
            if (Schema::hasColumn('accounting', 'meeting_time_id')) {
                DB::statement("ALTER TABLE `accounting` CHANGE COLUMN `meeting_time_id` `meeting_id` INTEGER UNSIGNED NULL;");
            }
        });
    }
}
