<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            // Item fields matching form
            $table->text('description')->nullable();
            $table->decimal('quantity', 18, 2)->default(1);
            $table->string('quantity_unit')->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0); // bisa persen atau nominal tergantung implementasi
            $table->boolean('tax_included')->default(false);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('gross_up', 20, 2)->default(0);

            // computed
            $table->decimal('line_subtotal', 20, 2)->default(0); // sebelum diskon/pajak
            $table->decimal('line_total', 20, 2)->default(0); // akhir per item

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
