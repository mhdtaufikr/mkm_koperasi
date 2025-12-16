<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengantar - {{ $deliveryNote->delivery_note_no }}</title>

    <style>
        /* Margin kertas untuk PDF: lebih lega kiri-kanan */
        @page {
            margin: 28px 48px; /* atas-bawah | kiri-kanan (lebih besar) */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Wrapper supaya isi nggak nempel tepi (tambahan ruang lagi) */
        .page {
            padding: 8px 10px; /* top-bottom | left-right */
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h3 {
            font-size: 20px;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        /* TITLE SECTION */
        .title-section {
            margin: 20px 0 10px 0;
        }

        .title-section h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title-section .info {
            font-size: 12px;
            margin: 2px 0;
        }

        .intro-text {
            margin: 15px 0;
            font-size: 12px;
        }

        /* TABLE TANPA GARIS */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }

        .items-table th,
        .items-table td {
            border: none;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
            word-break: break-word;
        }

        .items-table th {
            font-weight: bold;
            text-align: left;
        }

        .items-table td.number { width: 6%; }
        .items-table td.item-name { width: 40%; }
        .items-table td.quantity { width: 14%; }
        .items-table td.unit { width: 12%; }
        .items-table td.description { width: 28%; }

        .footer-code {
            margin-top: 10px;
            font-size: 10px;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 30px;
        }

        .signature-table {
            width: 100%;
        }

        .signature-box {
            text-align: center;
            width: 50%;
            padding: 0 20px;
        }

        .signature-box p {
            margin: 5px 0;
            font-size: 11px;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            margin-top: 5px;
            font-weight: bold;
            text-decoration: underline;
        }

        .page-break {
            page-break-after: always;
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
        @if($deliveryNote->vehicle_no)
            <div class="info">Kendaraan No: <strong>{{ $deliveryNote->vehicle_no }}</strong></div>
        @endif
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
                <td class="signature-box" style="text-align:right; padding-right:40px;">
                    {{ $deliveryNote->location }},
                    {{ \Carbon\Carbon::parse($deliveryNote->delivery_date)->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
        </table>

        <table class="signature-table" style="margin-top:20px;">
            <tr>
                <td class="signature-box">
                    <p>Yang menerima</p>
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        @if($deliveryNote->receiver_name)
                            {{ strtoupper($deliveryNote->receiver_name) }}
                        @else
                            ( ................................ )
                        @endif
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
    <div style="margin-top:20px; padding:10px; border:1px solid #ccc; background:#f9f9f9;">
        <strong>Catatan:</strong> {{ $deliveryNote->notes }}
    </div>
    @endif

</div>
</body>
</html>
