@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Buat Invoice</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('invoice.store') }}" method="POST" id="invoiceForm">
        @csrf

        <div class="card mb-3 p-3">
            <h5>Header Invoice</h5>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Client</label>
                    <input type="text" name="client" class="form-control" value="{{ old('client') }}" required>
                </div>

                <div class="col-md-6 mb-2">
                    <label>PIC</label>
                    <input type="text" name="pic" class="form-control" value="{{ old('pic') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>No PO</label>
                    <input type="text" name="no_po" class="form-control" value="{{ old('no_po') }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label>No Invoice (biarkan kosong untuk generate otomatis)</label>
                    <input type="text" name="no_invoice" class="form-control" value="{{ old('no_invoice') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit" class="form-control" value="{{ old('tanggal_terbit', date('Y-m-d')) }}">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Tanggal Jatuh Tempo</label>
                    <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="{{ old('tanggal_jatuh_tempo') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Tujuan Pembayaran</label>
                    <input type="text" name="tujuan_pembayaran" class="form-control" value="{{ old('tujuan_pembayaran') }}">
                </div>

                <div class="col-md-6 mb-2">
                    <label>Tertanda</label>
                    <input type="text" name="tertanda" class="form-control" value="{{ old('tertanda') }}">
                </div>

                <div class="col-md-12 mb-2">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card p-3 mb-3">
            <h5>Detail Item Invoice</h5>

            <div id="items">
                {{-- Jika ada old() items, render kembali --}}
                @if(old('items'))
                    @foreach(old('items') as $i => $it)
                        @include('invoice.partials.item_row', ['index' => $i, 'item' => $it])
                    @endforeach
                @else
                    @include('invoice.partials.item_row', ['index' => 0, 'item' => null])
                @endif
            </div>

            <div class="mt-3">
                <button type="button" id="addItem" class="btn btn-success">+ Tambah Item</button>
            </div>

            <div class="mt-4 text-right">
                <h4>Jumlah: <span id="grandTotalTop">Rp0,00</span></h4>
            </div>
        </div>

        {{-- preview singkat (optional) --}}
        @include('invoice.partials.preview_panel')

        <button class="btn btn-primary">Simpan Invoice</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        let itemIndex = document.querySelectorAll('.item-row').length || 1;

        document.getElementById('addItem').addEventListener('click', function(){
            const container = document.getElementById('items');
            const idx = itemIndex;
            const template = `
            <div class="item-row card mb-2 p-3" data-index="${idx}">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label>Deskripsi</label>
                        <textarea name="items[${idx}][description]" class="form-control"></textarea>
                    </div>

                    <div class="col-md-2">
                        <label>Kuantitas</label>
                        <input type="number" step="any" name="items[${idx}][quantity]" class="form-control item-quantity" value="1">
                    </div>
                    <div class="col-md-2">
                        <label>Unit</label>
                        <input type="text" name="items[${idx}][quantity_unit]" class="form-control" value="">
                    </div>
                    <div class="col-md-3">
                        <label>Harga Per Unit</label>
                        <input type="number" step="any" name="items[${idx}][price]" class="form-control item-price" value="0">
                    </div>
                    <div class="col-md-2">
                        <label>Diskon (%)</label>
                        <input type="number" step="any" name="items[${idx}][discount]" class="form-control item-discount" value="0">
                    </div>
                    <div class="col-md-1">
                        <label>Pajak %</label>
                        <input type="number" step="any" name="items[${idx}][tax_percent]" class="form-control item-tax" value="0">
                    </div>
                    <div class="col-md-2">
                        <label>Gross Up</label>
                        <input type="number" step="any" name="items[${idx}][gross_up]" class="form-control item-gross" value="0">
                    </div>

                    <div class="col-md-12 mt-2 text-right">
                        <button type="button" class="btn btn-danger btn-sm remove-item">Hapus Item</button>
                    </div>
                </div>
            </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            itemIndex++;
            attachInputListeners();
        });

        function attachInputListeners(){
            document.querySelectorAll('.remove-item').forEach(btn=>{
                btn.onclick = function(){
                    this.closest('.item-row').remove();
                    calcTotal();
                };
            });
            document.querySelectorAll('.item-quantity, .item-price, .item-discount, .item-tax, .item-gross').forEach(el=>{
                el.oninput = calcTotal;
            });
        }

        // terbilang function (Indonesian)
        function terbilang(total) {
            if (!Number.isFinite(total)) return '';

            const satuan = ["","Satu","Dua","Tiga","Empat","Lima","Enam","Tujuh","Delapan","Sembilan","Sepuluh","Sebelas"];
            function inWords(n) {
                n = Math.floor(n);
                if (n < 12) return satuan[n];
                if (n < 20) return inWords(n - 10) + " Belas";
                if (n < 100) return inWords(Math.floor(n/10)) + " Puluh" + (n%10? " " + inWords(n%10): "");
                if (n < 200) return "Seratus" + (n-100? " " + inWords(n-100): "");
                if (n < 1000) return inWords(Math.floor(n/100)) + " Ratus" + (n%100? " " + inWords(n%100): "");
                if (n < 2000) return "Seribu" + (n-1000? " " + inWords(n-1000): "");
                if (n < 1000000) return inWords(Math.floor(n/1000)) + " Ribu" + (n%1000? " " + inWords(n%1000): "");
                if (n < 1000000000) return inWords(Math.floor(n/1000000)) + " Juta" + (n%1000000? " " + inWords(n%1000000): "");
                if (n < 1000000000000) return inWords(Math.floor(n/1000000000)) + " Miliar" + (n%1000000000? " " + inWords(n%1000000000): "");
                return n.toString();
            }

            if (total === 0) return "Nol Rupiah";
            const absVal = Math.abs(Math.floor(total));
            const words = inWords(absVal);
            return (total < 0 ? "Minus " : "") + words + " Rupiah";
        }

        function formatIDR(n){
            if (!Number.isFinite(n)) return 'Rp 0,00';
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n);
        }

        function calcTotal(){
        let itemSubtotal = 0;   // sum of line subtotal (qty*price)
        let discountSum = 0;    // sum discount amount across items
        let taxSum = 0;         // sum tax amount across items
        let grossUpSum = 0;     // sum gross up across items

        document.querySelectorAll('.item-row').forEach(row=>{
            const q = parseFloat(row.querySelector('.item-quantity')?.value || 0);
            const p = parseFloat(row.querySelector('.item-price')?.value || 0);
            let d = parseFloat(row.querySelector('.item-discount')?.value || 0);
            const t = parseFloat(row.querySelector('.item-tax')?.value || 0);
            const g = parseFloat(row.querySelector('.item-gross')?.value || 0);

            const lineSubtotal = q * p;

            // Interpretasi diskon: jika d <= 100 treat as percent, else nominal
            let lineDiscount = 0;
            if (d > 0 && d <= 100) {
                lineDiscount = (lineSubtotal) * (d/100); // persen
            } else {
                lineDiscount = d; // nominal
            }

            const lineTax = (lineSubtotal - lineDiscount) * (t/100);
            const lineGross = g;

            itemSubtotal += lineSubtotal;
            discountSum += lineDiscount;
            taxSum += lineTax;
            grossUpSum += lineGross;
        });

        // Subtotal preview menampilkan item subtotal + gross up
        const previewSubtotal = itemSubtotal + grossUpSum;

        // PPh23 dihitung dari previewSubtotal sebagai PENAMBAH (bukan pemotongan)
        const pphPercent = parseFloat(document.getElementById('pph23_input')?.value || 0);
        const pphAmount = Math.round((previewSubtotal * (pphPercent/100)) * 100) / 100;

        // Grand total = subtotal (item + grossUp) - discountSum + taxSum + pphAmount
        const grand = previewSubtotal - discountSum + taxSum + pphAmount;

        // Update preview DOM
        document.getElementById('preview_gross_up').innerText = formatIDR(grossUpSum);
        document.getElementById('preview_subtotal').innerText = formatIDR(previewSubtotal);
        // tampilkan PPh sebagai penambah dengan tanda plus
        document.getElementById('preview_pph23').innerText = '+' + formatIDR(pphAmount);
        document.getElementById('preview_diskon').innerText = '-' + formatIDR(discountSum);
        document.getElementById('preview_pajak').innerText = formatIDR(taxSum);
        document.getElementById('preview_grand_total').innerText = formatIDR(grand);

        // update top grand
        const grandTopEl = document.getElementById('grandTotalTop') || document.getElementById('grandTotal');
        if (grandTopEl) grandTopEl.innerText = formatIDR(grand);

        // update terbilang
        document.getElementById('terbilang').innerText = terbilang(Math.round(grand));

        // --- optional: simpan nilai-nilai total ke hidden inputs sebelum submit ---
        // bila kamu menambahkan <input type="hidden" name="subtotal"> dsb pada form,
        // maka update nilainya di sini supaya backend menerima angka final.
        const form = document.getElementById('invoiceForm');
        if (form) {
            const setIfExists = (name, value) => {
                const el = form.querySelector(`input[name="${name}"]`);
                if (el) el.value = value;
            };
            setIfExists('subtotal', itemSubtotal);
            setIfExists('gross_up_total', grossUpSum);
            setIfExists('discount_total', discountSum);
            setIfExists('tax_total', taxSum);
            setIfExists('pph23_total', pphAmount);
            setIfExists('total', grand);
        }
    }


        // re-calc saat PPh23 diubah
        document.getElementById('pph23_input').addEventListener('input', calcTotal);

        attachInputListeners();
        calcTotal();
    });
    </script>

@endsection
