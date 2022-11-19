<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->decimal('price');
            $table->decimal('offer_price')->nullable();
            $table->boolean('has_discount')->nullable();
            $table->float('rate')->nullable();
            $table->timestamps();
            $table->timestamp('locked_at')->nullable();
                   });

                   Schema::create('product_translations', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('product_id');
                    $table->string('name');
                    $table->text('description');
                    $table->string('locale')->index();
                    $table->unique(['product_id', 'locale']);
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');            
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
        Schema::dropIfExists('products');
    }
}
