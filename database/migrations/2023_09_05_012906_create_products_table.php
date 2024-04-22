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
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('category');
            $table->text('title')->nullable();
            $table->text('description');
            $table->string('rating')->default(0);
            $table->string('ratings_number')->default(0);
            $table->string('price');
            $table->string('discount')->default(0);
            $table->text('about')->nullable();
            //$table->enum('status', ['published', 'draft'])->default('draft');
            //old zip
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('product_origin')->nullable();
            $table->text('effective_material')->nullable();
            $table->string('color')->nullable();
            $table->string('shape')->nullable();
            $table->string('code')->nullable();
            $table->string('image')->nullable();
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
