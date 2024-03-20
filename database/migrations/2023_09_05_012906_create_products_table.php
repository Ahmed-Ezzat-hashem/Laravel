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
            //PHARMACY ID
            $table->foreignId('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            $table->unsignedBigInteger('category')->nullable();
            $table->text('title')->nullable();
            $table->text('description');
            $table->string('rating')->default(0);
            $table->string('ratings_number')->default(0);
            $table->string('price');
            $table->string('discount')->default(0);
            $table->text('about');
            //$table->enum('status', ['published', 'draft'])->default('draft');
            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            //old zip
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('product_origin');
            $table->text('effective_material');
            $table->string('color')->nullable();
            $table->string('shap')->nullable();
            $table->string('code');
            $table->string('image');
            $table->timestamps();
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
