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
        Schema::create('customer_sites', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('site_name');
            $table->string('customer_id');
            $table->string('site_email')->nullable()->unique();
            $table->string('site_address')->nullable();
            $table->integer('site_state_id')->nullable();
            $table->integer('site_lga_id')->nullable();
            $table->integer('site_zone_id')->nullable();
            $table->string('rate')->nullable()->comment("this holds the value agreed between both parties");
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sites');
    }
};
