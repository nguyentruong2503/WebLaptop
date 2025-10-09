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
       Schema::create('promotions', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('applied_type', ['product', 'category', 'brand', 'global'])->default('product');
    $table->unsignedBigInteger('category_id')->nullable();
    $table->unsignedBigInteger('brand_id')->nullable();
    $table->decimal('discount_percent', 5, 2)->nullable();
    $table->decimal('discount_amount', 10, 2)->nullable();
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('category_id')->references('id')->on('product_types')->onDelete('set null');
    $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
