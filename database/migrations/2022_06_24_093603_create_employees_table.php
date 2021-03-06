<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('employer_code');
            $table->string('acct_no')->nullable()->unique();
            $table->string('address');
            $table->string('state_of_residence');
            $table->string('nk_surname');
            $table->string('nk_firstname');
            $table->string('nk_phone');
            $table->string('nk_email');
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
        Schema::dropIfExists('employees');
    }
}
