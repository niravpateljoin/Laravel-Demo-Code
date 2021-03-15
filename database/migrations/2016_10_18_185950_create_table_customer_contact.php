<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomerContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contact', function (Blueprint $table) {
            $table->increments('contact_id');
			$table->integer('contact_customer_id');
            $table->string('contact_fname',50);
			$table->string('contact_lname',50);
            $table->string('contact_email',50)->unique();
            $table->string('contact_direct_no',20);
            $table->rememberToken();
            $table->timestamps();
			$table->boolean('contact_is_delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_contact');
    }
}
