@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Invoice {{ $invoice->no_invoice }}</h3>

    <a href="{{ route('invoice.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card p-3 mb-3">
        <h5>Header</h5>
        <p><strong>PIC:</strong> {{ $invoice->pic }}</p>
        <p><strong>No PO:</strong> {{ $invoice->no_po }}</p>
        <p><strong>Tanggal Terbit:</strong> {{ optional($invoice->tanggal_terbit)->format('Y-m-d') }}</p>
        <p><strong>Tanggal Jatuh Tempo:</strong> {{ optional($invoice->tanggal_jatuh_tempo)->format('Y-m-d') }}</p>
    </div>

    <div class="card p-3">
        <h5>Items</h5>
        <table class="table">
            <thead>
                <tr><th>#</th><th>Deskripsi</th><th>Qty</th><th>Price</th><th>Diskon</th><th>Pajak</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }} {{ $item->quantity_unit }}</td>
                        <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                        <td>Rp {{ number_format($item->discount,0,',','.') }}</td>
                        <td>{{ $item->tax_percent }}%</td>
                        <td>Rp {{ number_format($item->line_total,0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right">
            <p>Subtotal: Rp {{ number_format($invoice->subtotal,0,',','.') }}</p>
            <p>Pajak: Rp {{ number_format($invoice->tax_total,0,',','.') }}</p>
            <h4>Total: Rp {{ number_format($invoice->total,0,',','.') }}</h4>
        </div>
    </div>
</div>
@endsection
