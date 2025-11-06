<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('subscribe_translations')) {
            // ✅ Kiểm tra cột description có tồn tại không
            if (Schema::hasColumn('subscribe_translations', 'description')) {
                DB::statement("ALTER TABLE `subscribe_translations` CHANGE `description` `subtitle` TEXT NULL;");
            }

            // ✅ Thêm lại cột description mới
            Schema::table('subscribe_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('subscribe_translations', 'description')) {
                    $table->text('description')->nullable()->after('subtitle');
                }
            });
        }
    }
};
