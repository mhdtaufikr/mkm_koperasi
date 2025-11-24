@php
    $index = $index ?? 0;
    $it = $item ?? [];
@endphp

<div class="item-row card mb-2 p-3" data-index="{{ $index }}">
    <div class="row">
        <div class="col-md-12 mb-2">
            <label>Deskripsi</label>
            <textarea name="items[{{ $index }}][description]" class="form-control">{{ $it['description'] ?? '' }}</textarea>
        </div>

        <div class="col-md-2">
            <label>Kuantitas</label>
            <input type="number" step="any" name="items[{{ $index }}][quantity]" class="form-control item-quantity" value="{{ $it['quantity'] ?? 1 }}">
        </div>
        <div class="col-md-2">
            <label>Unit</label>
            <input type="text" name="items[{{ $index }}][quantity_unit]" class="form-control" value="{{ $it['quantity_unit'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label>Harga Per Unit</label>
            <input type="number" step="any" name="items[{{ $index }}][price]" class="form-control item-price" value="{{ $it['price'] ?? 0 }}">
        </div>
        <div class="col-md-2">
            <label>Diskon (%)</label>
            <input type="number" step="any" name="items[{{ $index }}][discount]" class="form-control item-discount" value="{{ $it['discount'] ?? 0 }}">
        </div>
        <div class="col-md-1">
            <label>Pajak %</label>
            <input type="number" step="any" name="items[{{ $index }}][tax_percent]" class="form-control item-tax" value="{{ $it['tax_percent'] ?? 0 }}">
        </div>
        <div class="col-md-2">
            <label>Gross Up</label>
            <input type="number" step="any" name="items[{{ $index }}][gross_up]" class="form-control item-gross" value="{{ $it['gross_up'] ?? 0 }}">
        </div>

        <div class="col-md-12 mt-2 text-right">
            <button type="button" class="btn btn-danger btn-sm remove-item">Hapus Item</button>
        </div>
    </div>
</div>
