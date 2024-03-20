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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            //PHONE USER ID
            $table->unsignedBigInteger('user_id');
            //PHARMACY ID
            $table->unsignedBigInteger('pharmacy_id');
            $table->unsignedTinyInteger('rating'); // Rating value (1 to 5 stars)
            $table->timestamps();

            $table->unique(['user_id', 'pharmacy_id']); // Each user can rate a pharmacy only once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
