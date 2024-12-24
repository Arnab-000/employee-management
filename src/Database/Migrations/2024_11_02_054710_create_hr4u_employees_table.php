<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr4u_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('division')->nullable();
            $table->string('department')->nullable();
            $table->string('unit')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('position_id');
            $table->string('designation')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->integer('hierarchy_manager_id')->nullable();
            $table->string('hierarchy_manager_name')->nullable();
            $table->string('hierarchy_manager_email')->nullable();
            $table->integer('hierarchy_manager_position_id')->nullable();
            $table->string('position_text')->nullable();
            $table->integer('division_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->string('cost_center_description')->nullable();
            $table->string('employee_band')->nullable();
            $table->integer('second_level_supervisor_id')->nullable();
            $table->integer('third_level_supervisor_id')->nullable();
            $table->string('gender', 128)->nullable();
            $table->string('office_location', 512)->nullable();
            $table->string('company', 128)->nullable();
            $table->date('joining_date')->nullable();
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
        Schema::dropIfExists('hr4u_employees');
    }
};
