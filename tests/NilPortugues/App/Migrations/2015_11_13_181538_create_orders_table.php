<?php

namespace NilPortugues\Tests\App\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id', true);

            $table->integer('employee_id')->nullable()->index('employee_id_2');
            $table->integer('customer_id')->nullable()->index('customer_id_2');
            $table->dateTime('order_date')->nullable();
            $table->dateTime('shipped_date')->nullable();
            $table->integer('shipper_id')->nullable()->index('shipper_id_2');
            $table->string('ship_name', 50)->nullable();
            $table->text('ship_address')->nullable();
            $table->string('ship_city', 50)->nullable();
            $table->string('ship_state_province', 50)->nullable();
            $table->string('ship_zip_postal_code', 50)->nullable()->index('ship_zip_postal_code');
            $table->string('ship_country_region', 50)->nullable();
            $table->decimal('shipping_fee', 19, 4)->nullable()->default(0.0000);
            $table->decimal('taxes', 19, 4)->nullable()->default(0.0000);
            $table->string('payment_type', 50)->nullable();
            $table->dateTime('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->boolean('tax_status_id')->nullable()->index('tax_status');
            $table->boolean('status_id')->nullable()->default(0)->index('fk_orders_orders_status1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasTable('orders')) {
            Schema::drop('orders');
        }
    }
}
