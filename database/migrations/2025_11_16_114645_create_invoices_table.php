<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Header fields (sesuaikan dengan form di screenshot)
            $table->unsignedBigInteger('client_id')->nullable(); // refer ke mst klien (jika ada)
            $table->string('pic')->nullable();
            $table->string('no_po')->nullable();
            $table->string('no_invoice')->nullable()->unique();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->string('tujuan_pembayaran')->nullable();
            $table->string('tertanda')->nullable();
            $table->text('notes')->nullable();

            // totals
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_total', 20, 2)->default(0);
            $table->decimal('tax_total', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);

            // status / publish
            $table->boolean('is_published')->default(false);
            $table->enum('status', ['pending','paid','partial','cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
