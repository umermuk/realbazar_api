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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('username')->nullable();
            $table->string('person_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('shop_number')->nullable();
            $table->string('market_name')->nullable();
            $table->string('store_name')->nullable();
            $table->string('cnic_number')->nullable();
            $table->string('bio')->nullable();
            $table->string('website')->nullable();
            $table->enum('account_type', ['facebook', 'google','apple'])->nullable();
            $table->string('account_id')->nullable();
            $table->string('family_name')->nullable();
            $table->string('given_name')->nullable();
            $table->mediumText('image')->nullable();
            $table->mediumText('bill_image')->nullable();
            $table->mediumText('token')->nullable();
            $table->mediumText('device_token')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_block')->default(false);
            $table->boolean('is_user_app')->default(false);
            $table->string('price')->nullable();
            $table->string('txt_refno')->nullable();
            $table->string('response_code')->nullable();
            $table->string('response_message')->nullable();
            $table->enum('payment_method',['easypaisa','jazzcash'])->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
