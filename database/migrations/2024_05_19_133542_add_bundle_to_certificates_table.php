<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // ✅ Chỉnh bảng certificates
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                // Sửa cột type nếu tồn tại
                if (Schema::hasColumn('certificates', 'type')) {
                    DB::statement("
                        ALTER TABLE `certificates`
                        MODIFY COLUMN `type` enum('quiz', 'course', 'bundle')
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                        NOT NULL AFTER `user_grade`
                    ");
                }

                // Thêm bundle_id nếu chưa có
                if (!Schema::hasColumn('certificates', 'bundle_id')) {
                    $table->unsignedInteger('bundle_id')->nullable()->after('webinar_id');
                    $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('cascade');
                }
            });
        }

        // ✅ Chỉnh bảng certificates_templates
        if (Schema::hasTable('certificates_templates')) {
            Schema::table('certificates_templates', function (Blueprint $table) {
                if (Schema::hasColumn('certificates_templates', 'type')) {
                    DB::statement("
                        ALTER TABLE `certificates_templates`
                        MODIFY COLUMN `type` enum('quiz', 'course', 'bundle')
                        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                        NOT NULL AFTER `image`
                    ");
                } else {
                    // Nếu chưa có thì thêm mới
                    $table->enum('type', ['quiz', 'course', 'bundle'])->default('course')->after('image');
                }
            });
        }
    }
};
