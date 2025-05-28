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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userID');
            $table->decimal('totalAmount', 10, 2);
            $table->string('fullName');
            $table->string('phone');
            $table->string('address');
            $table->enum('orderstatus', ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'])->default('Pending');
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
