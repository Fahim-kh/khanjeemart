<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Module extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('route')->default('#');
            $table->string('icon');
            $table->integer('parent_id')->nullable();
            $table->integer('sorting')->nullable();
            $table->boolean('is_group_title')->default(false);
            $table->string('color')->nullable()->default('primary-600');
            $table->string('icon_type')->default('class'); // 'class' or 'html'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module');
    }
}
