<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationsTable extends Migration
{
    public function up()
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('brand');           // Thương hiệu
            $table->string('model');           // Mẫu TechPro
            $table->string('connection');      // Kết nối
            $table->string('layout');          // Cách trình bày
            $table->string('switch');          // Công tắc
            $table->string('lighting');        // Đèn nền
            $table->string('compatibility');   // Tương thích
            $table->string('dimensions');      // Kích thước
            $table->string('weight');          // Trọng lượng
            $table->string('warranty');        // Bảo hành
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_specifications');
    }
}
