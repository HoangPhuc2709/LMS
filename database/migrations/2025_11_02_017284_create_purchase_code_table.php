<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_code', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->enum('product_type', ['main', 'plugin_bundle', 'theme_builder', 'mobile_app'])->default('main');
            $table->string('license_type')->default('Regular license');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_code');
    }
};
