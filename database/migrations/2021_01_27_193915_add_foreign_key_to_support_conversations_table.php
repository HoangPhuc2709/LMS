<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeyToSupportConversationsTable extends Migration
{
    public function up()
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            // kiểm tra nếu chưa có khóa ngoại thì mới tạo
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = 'support_conversations'
                AND TABLE_SCHEMA = DATABASE()
            ");

            $existingKeys = array_column($foreignKeys, 'CONSTRAINT_NAME');

            if (!in_array('support_conversations_support_id_foreign', $existingKeys)) {
                $table->foreign('support_id')->references('id')->on('supports')->onDelete('cascade');
            }

            if (!in_array('support_conversations_sender_id_foreign', $existingKeys)) {
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('support_conversations', function (Blueprint $table) {
            $table->dropForeign(['support_id']);
            $table->dropForeign(['sender_id']);
        });
    }
}
