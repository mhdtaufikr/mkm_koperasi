<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client','pic','no_po','no_invoice','tanggal_terbit','tanggal_jatuh_tempo',
        'tujuan_pembayaran','tertanda','notes',
        'subtotal','discount_total','tax_total','total',
        'is_published','status'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'is_published' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
