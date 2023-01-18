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
        Schema::create('bog_pay_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('locale');
            $table->double('amount');
            $table->string('order_id');
            $table->boolean('is_paid')->default(0);
            $table->timestamp('completed_at')->nullable();
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
        Schema::dropIfExists('bog_pay_transactions');
    }
};
