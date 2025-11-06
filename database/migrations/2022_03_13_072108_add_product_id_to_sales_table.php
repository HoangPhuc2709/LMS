<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProductIdToSalesTable extends Migration
{
    public function up()
    {
        // B1. Thêm cột mới vào sales
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('product_order_id')->nullable()->after('promotion_id');
        });

        // B2. Sửa lại enum type trong sales (sau khi cột đã có)
        DB::statement("
            ALTER TABLE `sales`
            MODIFY COLUMN `type`
            ENUM('webinar','meeting','subscribe','promotion','registration_package','product')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            NOT NULL
            AFTER `registration_package_id`
        ");

        // B3. Thêm cột vào order_items (đặt sau 'promotion_id' thay vì 'product_id')
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('product_order_id')->nullable()->after('promotion_id');
        });

        // B4. Thêm cột product_id vào accounting
        Schema::table('accounting', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->nullable()->after('registration_package_id');
        });
    }
}
