<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormDataTable extends Migration
{
    public function up()
    {
        Schema::create('form_data', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('form_builder_id')->constrained('form_builders')->onDelete('cascade');
            $table->json('form_field_answers');
            $table->unsignedBigInteger('task_id')->nullable()->comment("This column would hold the task id");
            $table->unsignedBigInteger('entity_id')->nullable()->comment("THis could hold the customer id or supplier id");
            $table->string('entity_type')->nullable()->comment("This column helps us determine if the id entity is a customer or a supplier ");
            $table->string('updated_entity_table')->nullable()->comment("this holds the full class path name of the modified model");
            $table->string('updated_entity_id')->nullable()->comment("this holds the id of the modified model");
            $table->unsignedBigInteger('automator_flow_id')->nullable()->comment("This column would hold the automator id, which can comes from processflow service or from automator service");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_data');
    }
}
