<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengantar - {{ $deliveryNote->delivery_note_no }}</title>

    <style>
        /* 🚨 Margin kertas diperbesar signifikan */
        @page {
            margin-top: 30px;
            margin-bottom: 30px;
            margin-left: 70px;   /* INI YANG PENTING */
            margin-right: 70px;  /* INI YANG PENTING */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Wrapper utama */
        .page {
            padding: 10px 20px; /* tambah ruang kiri-kanan lagi */
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h3 {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 4px 0;
            font-size: 12px;
        }

        /* TITLE */
        .title-section {
            margin: 20px 0 12px 0;
        }

        .title-section h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .title-section .info {
            font-size: 12px;
            margin: 3px 0;
        }

        .intro-text {
            margin: 18px 0;
            font-size: 12px;
        }

        /* TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px 0;
        }

        .items-table th,
        .items-table td {
            border: none;
            padding: 8px 10px; /* lebih longgar */
            font-size: 11px;
            vertical-align: top;
            word-break: break-word;
        }

        .items-table th {
            font-weight: bold;
            text-align: left;
        }

        .items-table td.number { width: 6%; }
        .items-table td.item-name { width: 38%; }
        .items-table td.quantity { width: 14%; }
        .items-table td.unit { width: 12%; }
        .items-table td.description { width: 30%; }

        .footer-code {
            margin-top: 15px;
            font-size: 10px;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 40px;
        }

        .signature-table {
            width: 100%;
        }

        .signature-box {
            width: 50%;
            text-align: center;
            padding: 0 30px; /* BIKIN TTD GAK NEMPEL */
        }

        .signature-box p {
            margin: 6px 0;
            font-size: 11px;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            margin-top: 6px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* CATATAN */
        .notes {
            margin-top: 30px;
            padding: 12px 16px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            font-size: 11px;
        }
    </style>
</head>

<body>
<div class="page">

    <div class="header">
        <h3>KOPERASI KARYAWAN MITSUBISHI KRAMAYUDHA MOTORS</h3>
        <p><strong>Perdagangan Umum, Suppliers, Contractors</strong></p>
        <p>Jl. Raya Bekasi KM-21 Pulo Gadung, Jakarta Timur, PO BOX 3348/JKT 10033</p>
        <p>Telp. : 4602908 - 4602911 Fax. : 4602915</p>
    </div>

    <div class="title-section">
        <h4>SURAT PENGANTAR</h4>
        <div class="info">No: <strong>{{ $deliveryNote->delivery_note_no }}</strong></div>
        <div class="info">Kendaraan No: <strong>{{ $deliveryNote->vehicle_no ?? '-' }}</strong></div>
    </div>

    <div class="intro-text">
        Harap diterima dengan baik, barang-barang seperti tersebut di bawah ini :
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>NO.</th>
                <th>NAMA BARANG</th>
                <th>BANYAKNYA</th>
                <th>SATUAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td class="item-name">{{ strtoupper($item->item_name) }}</td>
                <td class="quantity">{{ $item->quantity }}</td>
                <td class="unit">{{ strtoupper($item->unit) }}</td>
                <td class="description">{{ $item->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-code">
        {{ $deliveryNote->footer_code }}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-box" style="text-align:left;">
                    {{ $deliveryNote->location }},
                    {{ \Carbon\Carbon::parse($deliveryNote->delivery_date)->locale('id')->translatedFormat('d F Y') }}
                </td>
                <td class="signature-box" style="text-align:right;">
                    KOPKAR MKM
                </td>
            </tr>
        </table>

        <table class="signature-table" style="margin-top:25px;">
            <tr>
                <td class="signature-box">
                    <p>Yang menerima</p>
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ strtoupper($deliveryNote->receiver_name ?? 'PT. MKM') }}
                    </div>
                    <p style="font-size:10px;">(Nama Jelas)</p>
                </td>
                <td class="signature-box">
                    <p>{{ strtoupper($deliveryNote->sender_name ?? 'KOPKAR MKM') }}</p>
                    <div class="signature-space"></div>
                    <div class="signature-name">DESI</div>
                </td>
            </tr>
        </table>
    </div>

    @if($deliveryNote->notes)
    <div class="notes">
        <strong>Catatan:</strong> {{ $deliveryNote->notes }}
    </div>
    @endif

</div>
</body>
</html>
