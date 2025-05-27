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
         Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('productName');
            $table->unsignedBigInteger('id_type');
            $table->unsignedBigInteger('id_branch');
            $table->decimal('price', 10, 2);
            $table->integer('quality');
            $table->string('img')->nullable();
            $table->timestamps();

            $table->foreign('id_type')->references('id')->on('product_types')->onDelete('cascade');
            $table->foreign('id_branch')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
