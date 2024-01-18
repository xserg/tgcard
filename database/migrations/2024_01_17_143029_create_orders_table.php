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
            $table->bigInteger('client_id');
            $table->integer('sum');
            $table->integer('sum_discount');
            $table->string('payment_type');
            $table->string('payment_info')->nullable();
            $table->string('card_num')->nullable();
            $table->string('card_date')->nullable();
            $table->string('cvc')->nullable();
            $table->string('pincode')->nullable();
            $table->dateTime('expired')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
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
