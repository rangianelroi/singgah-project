<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Sitaan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            padding: 10px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .page {
            background: white;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm 25mm;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: auto;
        }

        .logo-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .logo-container {
            width: 150px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
        }

        .logo-container p {
            color: #999;
            font-size: 9pt;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            flex: 1;
        }

        .header h1 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .header p {
            margin: 5px 0;
            font-size: 11pt;
        }

        .document-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
            line-height: 1.5;
        }

        .content {
            text-align: justify;
            margin-bottom: 12px;
            font-size: 11pt;
        }

        .content p {
            margin-bottom: 12px;
            text-indent: 0;
        }

        .content-title {
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-align: left;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 10pt;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        td {
            vertical-align: top;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .signature-title {
            line-height: 1.3;
        }

        .signature-date {
            margin-top: 20px;
            text-align: right;
            font-size: 11pt;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                padding: 20mm 25mm;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Logo Header --}}
        <div class="logo-header">
            <div class="logo-container">
                @php
                    $logoPath = null;
                    if(file_exists(public_path('assets/images/injourney-airports.png'))) {
                        $logoPath = public_path('assets/images/injourney-airports.png');
                    } elseif(file_exists(public_path('assets/images/injourney-airports.jpg'))) {
                        $logoPath = public_path('assets/images/injourney-airports.jpg');
                    } elseif(file_exists(public_path('assets/logo/injourney-airports.png'))) {
                        $logoPath = public_path('assets/logo/injourney-airports.png');
                    }
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Injourney Airports Logo" style="width: 150px; height: auto;">
                @else
                    <p>Logo Injourney Airports<br>(Letakkan file di public/assets/images/)</p>
                @endif
            </div>
            <div class="header">
                <h1>LAPORAN BARANG SITAAN</h1>
                <p><strong>CONFISCATED ITEMS REPORT</strong></p>
            </div>
        </div>

        {{-- Periode --}}
        <div class="document-info">
            <strong>Periode Laporan: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
        </div>

        {{-- Pendahuluan --}}
        <div class="content">
            <p>
                Laporan ini merupakan rekapitulasi data barang yang dicatat dan disita oleh petugas Aviation Security (AVSEC) selama periode laporan sebagaimana tercantum di atas.
            </p>
        </div>

        {{-- Judul Tabel --}}
        <div class="content-title">Daftar Barang Sitaan</div>

        {{-- Tabel Barang --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 18%;">Nama Penumpang</th>
                    <th style="width: 15%;">Penerbangan</th>
                    <th style="width: 20%;">Nama Barang</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->confiscation_date)->format('d/m/Y') }}</td>
                    <td>{{ $item->passenger->full_name ?? '-' }}</td>
                    <td>{{ $item->flight->airline->code ?? '-' }}{{ $item->flight->flight_number ?? '-' }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $item->category)) }}</td>
                    <td style="text-align: center;">{{ $item->item_quantity }} {{ $item->item_unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Jumlah Total --}}
        <div class="content" style="margin-top: 15px;">
            <p><strong>Total Barang Dicatat:</strong> {{ count($items) }} item</p>
        </div>

        {{-- Penutup --}}
        <div class="content">
            <p>
                Demikian laporan ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya sebagai arsip dan pertanggungjawaban pengelolaan barang sitaan Aviation Security.
            </p>
        </div>

        {{-- Tanda Tangan --}}
        <div class="signature-section">
            <div class="signature-date">
                Manado, {{ now()->format('d F Y') }}
            </div>

            <div style="margin-top: 40px;">
                <div class="signature-label">Mengetahui,</div>
            </div>
            
            <div style="margin-top: 60px;">
                <div class="signature-name">_________________________________</div>
                <div class="signature-title">AVIATION SECURITY DEPARTMENT HEAD</div>
            </div>
        </div>
    </div>
</body>
</html>