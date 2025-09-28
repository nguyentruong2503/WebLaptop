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
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['id_branch']); // bỏ ràng buộc khóa ngoại cũ
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('id_branch')->nullable()->change(); // cho phép null
            $table->foreign('id_branch')
                ->references('id')->on('brands')
                ->onDelete('set null'); // ràng buộc mới
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['id_branch']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('id_branch')->nullable(false)->change();
            $table->foreign('id_branch')
                ->references('id')->on('brands')
                ->onDelete('cascade');
        });
    }
};
