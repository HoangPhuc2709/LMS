<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRegistrationPackageIdToSalesTable extends Migration
{
    public function up()
    {
        // B1. Thêm cột mới vào sales
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('registration_package_id')->nullable()->after('promotion_id');
        });

        // B2. Sửa cột type (sau khi cột mới đã tồn tại)
        DB::statement("
            ALTER TABLE `sales`
            MODIFY COLUMN `type`
            ENUM('webinar','meeting','subscribe','promotion','registration_package')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            NOT NULL
            AFTER `registration_package_id`
        ");

        // B3. Thêm cột cho order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('registration_package_id')->nullable()->after('promotion_id');
        });

        // B4. Thêm cột cho accounting
        Schema::table('accounting', function (Blueprint $table) {
            $table->unsignedInteger('registration_package_id')->nullable()->after('promotion_id');
        });

        // B5. Sửa type_account trong accounting
        DB::statement("
            ALTER TABLE `accounting`
            MODIFY COLUMN `type_account`
            ENUM('income','asset','subscribe','promotion','registration_package')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            NULL DEFAULT NULL
            AFTER `type`
        ");
    }
}
