<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id','description','quantity','quantity_unit','price','discount',
        'tax_included','tax_percent','gross_up','line_subtotal','line_total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
