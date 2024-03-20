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
            //USER ID
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            //PHARMACY ID
            $table->foreignId('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['New Order', 'Complete', 'Rejected'])->default('New Order');
            $table->string('tracking_number')->nullable();
            $table->string('country');
            $table->string('street_name');
            $table->string('city');
            $table->string('state_province');
            $table->string('zip_code');
            $table->string('phone_number');
            $table->string('coupon_code')->nullable();
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
