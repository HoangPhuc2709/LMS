<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EditDiscountsTable extends Migration
{
    public function up()
    {
        // Xóa cột nếu có
        Schema::table('discounts', function (Blueprint $table) {
            if (Schema::hasColumn('discounts', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('discounts', 'started_at')) {
                $table->dropColumn('started_at');
            }
        });

        Schema::table('discount_users', function (Blueprint $table) {
            if (Schema::hasColumn('discount_users', 'count')) {
                $table->dropColumn('count');
            }
        });

        // Sửa cấu trúc cột và thêm mới
        Schema::table('discounts', function (Blueprint $table) {
            // chỉ sửa kiểu nếu cột tồn tại
            if (Schema::hasColumn('discounts', 'created_at')) {
                DB::statement("ALTER TABLE `discounts` MODIFY COLUMN `created_at` INT(10) UNSIGNED NOT NULL AFTER `expired_at`");
            }

            if (!Schema::hasColumn('discounts', 'title')) {
                $table->string('title')->after('creator_id');
            }

            if (!Schema::hasColumn('discounts', 'code')) {
                $table->string('code', 64)->after('title')->unique();
            }

            if (!Schema::hasColumn('discounts', 'type')) {
                $table->enum('type', ['all_users', 'special_users'])->after('count');
            }
        });
    }

    public function down()
    {
        // rollback cơ bản
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['title', 'code', 'type']);
        });
    }
}
