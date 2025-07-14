<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. 建立 task_user 中間表
        Schema::create('task_user', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary(['task_id', 'user_id']); // 設定複合主鍵
        });

        // 2. 從 tasks 表中移除 assigned_to 欄位（保險做法）
        Schema::table('tasks', function (Blueprint $table) {
            // 先嘗試移除外鍵，再移除欄位，兩步驟分開避免報錯
            try {
                $table->dropForeign(['assigned_to']); // 或 'tasks_assigned_to_foreign'
            } catch (\Exception $e) {
                // 忽略外鍵不存在
            }
            try {
                $table->dropColumn('assigned_to');
            } catch (\Exception $e) {
                // 忽略欄位不存在
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 回滾：tasks 表加回 assigned_to 欄位
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->after('created_by');
        });
        Schema::dropIfExists('task_user');
    }
};
