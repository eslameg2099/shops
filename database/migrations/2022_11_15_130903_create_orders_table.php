<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('status');
            $table->decimal('sub_total');
            $table->decimal('shipping_cost')->nullable()->default(0);
            $table->decimal('discount')->default(0);
            $table->unsignedInteger('payment_method');
            $table->decimal('total')->storedAs('(`sub_total` + CASE WHEN shipping_cost IS NULL THEN 0 ELSE shipping_cost END) - `discount`');
            $table->boolean('paid')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delegate_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('status');
            $table->decimal('sub_total');
            $table->decimal('shipping_cost')->nullable()->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('total')->storedAs('(`sub_total` + CASE WHEN shipping_cost IS NULL THEN 0 ELSE shipping_cost END) - `discount`');
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('delegate_collected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->Integer('option_id');
            $table->decimal('price');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total')->storedAs('(`price` * `quantity`)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
