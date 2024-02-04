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
            $table->unsignedBigInteger('category')->nullable();
            $table->text('title')->nullable();
            $table->text('description');
            $table->string('rating')->default(0);
            $table->string('ratings_number')->default(0);
            $table->string('price');
            $table->string('discount')->default(0);
            $table->text('About');
            $table->string('status')->default('draft');
            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            //old
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('product_origin');
            $table->text('effective_material');
            $table->string('color')->nullable();
            $table->string('shap')->nullable();
            $table->string('code');
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
