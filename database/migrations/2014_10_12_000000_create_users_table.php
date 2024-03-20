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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->unique();
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            //old
            $table->string('role')->default('0');
            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('facebook_token')->nullable();
            //pharmacy
            $table->string('company_name')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('delivary_area')->nullable();
            $table->string('company_working_hours')->nullable();
            $table->string('company_manager_name')->nullable();
            $table->string('company_manager_phone')->nullable();
            $table->string('commercial_register')->nullable();
            $table->string('tax_card')->nullable();
            $table->string('company_license')->nullable();
            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
