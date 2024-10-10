<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment("This column helps us determine if the id entity is a customer or a supplier ");
            $table->string('tag_class')->nullable()->comment("this holds the full class path name of the class to handle the form builder data");
            $table->string('tag_class_method')->comment("this holds the method that would take the needed action");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_data');
    }
}
