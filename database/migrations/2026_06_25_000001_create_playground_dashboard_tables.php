<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaygroundDashboardTables extends Migration
{
    public function up()
    {
        Schema::create('playground_dashboard_inputs', function (Blueprint $table) {
            $table->id();
            $table->longText('raw_data');
            $table->timestamps();
        });

        Schema::create('playground_financial_ratios', function (Blueprint $table) {
            $table->id();
            $table->string('category', 80);
            $table->string('ratio', 120)->unique();
            $table->decimal('value_2025', 10, 2)->default(0);
            $table->decimal('value_2024', 10, 2)->default(0);
            $table->decimal('diff', 10, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('playground_financial_items', function (Blueprint $table) {
            $table->id();
            $table->string('label', 160)->unique();
            $table->decimal('value_2025', 20, 2)->default(0);
            $table->decimal('value_2024', 20, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('playground_participations', function (Blueprint $table) {
            $table->id();
            $table->string('category', 80)->unique();
            $table->unsignedInteger('active_members')->default(0);
            $table->unsignedInteger('total_members')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('playground_participations');
        Schema::dropIfExists('playground_financial_items');
        Schema::dropIfExists('playground_financial_ratios');
        Schema::dropIfExists('playground_dashboard_inputs');
    }
}
