<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('order_date')->nullable();
            $table->string('status')->default('pending')->comment('Order status: pending, processing, completed, cancelled');
            $table->decimal('total_amount', 10, 2);

            // ✅ Thêm thông tin người nhận hàng ở đây:
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

