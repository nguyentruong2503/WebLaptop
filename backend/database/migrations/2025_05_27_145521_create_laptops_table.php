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
            $table->enum('GPU_type', ['Tích hợp','Rời'])->nullable();
            $table->string('expandable_slots')->nullable();
            $table->integer('battery_capacity_wh')->nullable();
            $table->integer('charging_watt')->nullable();
            $table->integer('USB_A_ports')->nullable();
            $table->integer('USB_C_ports')->nullable();
            $table->integer('HDMI_ports')->nullable();
            $table->integer('LAN_port')->nullable();
            $table->integer('Thunderbolt_ports')->nullable();
            $table->integer('jack_3_5mm')->nullable();
            $table->text('special_features')->nullable();
            $table->string('dimensions')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
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
