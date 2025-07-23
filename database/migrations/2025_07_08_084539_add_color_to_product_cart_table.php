<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorToProductCartTable extends Migration
{
    public function up()
    {
        Schema::table('product_cart', function (Blueprint $table) {
            $table->string('color')->default('black')->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('product_cart', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
