@extends('layouts.app')

@section('content')
<div class="container">
    <h3>List Invoice</h3>

    <a href="{{ route('invoice.create') }}" class="btn btn-success mb-3">+ Tambah Invoice Baru</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Client</th>
                <th>No Invoice</th>
                <th>No PO</th>
                <th>Tanggal Terbit</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $loop->iteration + ($invoices->currentPage()-1)*$invoices->perPage() }}</td>
                    <td>{{ $invoice->client }}</td>
                    <td>{{ $invoice->no_invoice }}</td>
                    <td>{{ $invoice->no_po }}</td>
                    <td>{{ optional($invoice->tanggal_terbit)->format('Y-m-d') }}</td>
                    <td>Rp. {{ number_format($invoice->total,0,',','.') }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>
                        <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-sm btn-info">Lihat</a>
                        <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="{{ route('invoice.exportPdf', $invoice) }}" class="btn btn-sm btn-primary">Export PDF</a>

                        <form action="{{ route('invoice.destroy', $invoice) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus invoice?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr><td colspan="8">Tidak ada invoice</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $invoices->links() }}
</div>
@endsection
