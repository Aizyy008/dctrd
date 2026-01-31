<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3)->index(); // USD, EUR, PKR, etc.
            $table->string('base_currency', 3)->default('USD'); // Base currency for rate
            $table->decimal('rate', 20, 8); // Exchange rate with high precision
            $table->string('source', 50)->nullable(); // API source name
            $table->boolean('is_fallback')->default(false); // Is this a fallback rate?
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['currency_code', 'base_currency']);
            $table->index('last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
}
