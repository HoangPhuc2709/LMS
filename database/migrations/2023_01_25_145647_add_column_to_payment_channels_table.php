<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPaymentChannelsTable extends Migration
{
    public function up()
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            // Bỏ "after('settings')" để tránh lỗi nếu cột đó không tồn tại
            $table->text('currencies')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropColumn('currencies');
        });
    }
}
