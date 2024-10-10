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
        Schema::create('gas_costs', function (Blueprint $table) {
            $table->id();
            $table->date('date_of_entry')->comment('date of entry');
            $table->float('dollar_cost_per_scf')->comment('dollar cost per scf');
            $table->float('dollar_rate')->comment('dollar rate in NGN per dollar cost per scf');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_costs');
    }
};
