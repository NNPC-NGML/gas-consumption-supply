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
        Schema::create('gas_situation_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("customer_id")->comment('customer id');
            $table->bigInteger("customer_site_id")->comment('customer site id');
            $table->float('inlet_pressure')->comment('inlet pressure in psi');
            $table->float('outlet_pressure')->comment('outlet pressure in psi');
            $table->float('allocation')->comment('allocation in MMscfd');
            $table->float('nomination')->comment('nomination in MMscfd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_situation_reports');
    }
};
