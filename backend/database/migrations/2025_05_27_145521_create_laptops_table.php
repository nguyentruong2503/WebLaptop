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
          Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('productID');
            $table->string('screenSpecs')->nullable();
            $table->string('CPU')->nullable();
            $table->string('RAM')->nullable();
            $table->string('SSD')->nullable();
            $table->string('GPU')->nullable();
            $table->text('des')->nullable();
            $table->timestamps();

            $table->foreign('productID')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laptops');
    }
};
