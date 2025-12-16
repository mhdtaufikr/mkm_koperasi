<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#222; }
        .header { display:flex; justify-content:space-between; margin-bottom:20px; }
        .company { text-align:left; }
        .meta { text-align:right; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #ddd; padding:8px; }
        th { background:#f5f5f5; }
        .right { text-align:right; }
        .no-border { border: none; }
        .small { font-size:11px; color:#555; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h3>KOPERASI KARYAWAN MKM</h3>
            <div class="small">Jakarta<br>email: sekretariat@kopkarmkm.id</div>
        </div>
        <div class="meta">
            <h2>INVOICE</h2>
            <div>No: <strong>{{ $invoice->no_invoice }}</strong></div>
            <div>Tanggal: {{ optional($invoice->tanggal_terbit)->format('Y-m-d') }}</div>
            <div>Tgl. Jatuh Tempo: {{ optional($invoice->tanggal_jatuh_tempo)->format('Y-m-d') }}</div>
        </div>
    </div>{{-- asd --}}

    <div>
        <strong>Kepada Yth:</strong>
        <div>{{ $invoice->client }}</div>
        @if($invoice->pic)<div>Attn: {{ $invoice->pic }}</div>@endif
        @if($invoice->no_po)<div>No. PO: {{ $invoice->no_po }}</div>@endif
    </div>

        <div style="margin-top:20px;">
            <strong>Jumlah Tertagih:</strong> Rp {{ number_format($invoice->total ?? 0,0,',','.') }}<br>
            <div style="margin-top:8px;">
                Pembayaran mohon ditransfer via rekening :<br>
                <div class="small">Bank Mandiri<br>Norek : 1250056000086<br>Atas Nama : KOPERASI KARYAWAN PT</div>
            </div>
        </div>

        <div class="signature">
            <div>Dengan Hormat,</div>
            <div style="height:60px;"></div>
            <div><strong>{{ $invoice->tertanda ?? 'Zuhadi' }}</strong></div>
        </div>
    </div>

    <div style="position:fixed; bottom:20px; right:40px; text-align:center;">
        <div>Dengan Hormat,</div>
        <div style="height:60px;"></div>
        <div><strong>{{ $invoice->tertanda ?? 'Zuhadi' }}</strong></div>
    </div>
</body>
</html>
