<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pemusnahan Barang Dilarang</title>
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
            height: 297mm;
        }

        .logo-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo-container {
            width: 150px;
            margin-right: 20px;
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 3px;
            line-height: 1.3;
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

        .center-text {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }

        .regulations {
            margin: 12px 0 12px 0;
            padding-left: 0;
        }

        .regulations ol {
            margin-left: 20px;
            padding-left: 0;
        }

        .regulations li {
            margin-bottom: 6px;
            line-height: 1.4;
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
            background-color: white;
            font-weight: bold;
            text-align: center;
        }

        td {
            vertical-align: top;
        }

        .execution-section {
            margin: 12px 0 12px 0;
        }

        .execution-section ol {
            margin-left: 20px;
            padding-left: 0;
        }

        .execution-section li {
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .closing {
            margin: 15px 0;
            text-align: justify;
            line-height: 1.4;
        }

        .signature-section {
            margin-top: 30px;
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
                    <img src="{{ $logoPath }}" alt="Injourney Airports Logo" style="width: 100px; height: auto;">
                @else
                    <p style="color: #999; font-size: 9pt;">Logo Injourney Airports<br>(Letakkan file di public/assets/images/)</p>
                @endif
            </div>
        </div>

        {{-- Header --}}
        <div class="header">
            <h1>BERITA ACARA PEMUSNAHAN BARANG<br>DILARANG (PROHIBITED ITEMS)</h1>
        </div>

        {{-- Nomor dan Tanggal --}}
        <div class="document-info">
            <strong>NOMOR : BAC.MDC.XYZ.{{ str_pad($disposalRecord->id, 4, '0', STR_PAD_LEFT) }}/XY.01/{{ $disposalRecord->disposal_date->format('Y') }}</strong><br>
            <strong>TANGGAL : {{ strtoupper($disposalRecord->disposal_date->translatedFormat('d F Y')) }}</strong>
        </div>

        {{-- Isi Berita Acara --}}
        <div class="content">
            <p>
                Pada hari ini, <strong>{{ ucfirst($disposalRecord->disposal_date->locale('en')->translatedFormat('l')) }}</strong> 
                tanggal <strong>{{ $disposalRecord->disposal_date->format('d') }}</strong> 
                bulan <strong>{{ ucfirst($disposalRecord->disposal_date->translatedFormat('F')) }}</strong> 
                tahun <strong>{{ $disposalRecord->disposal_date->format('Y') }}</strong> 
                ({{ $disposalRecord->disposal_date->format('d-m-Y') }}), kami yang bertanda tangan dibawah ini:
            </p>
            <p style="text-align: center;">
                --------------------------------------------------------------------------------------------
            </p>
        </div>

        <div class="center-text">
            AVIATION SECURITY DEPARTMENT HEAD
        </div>

        <div class="content">
            <p>
                Selaku Aviation Security Department Head PT Angkasa Pura Indonesia Bandar Udara Internasional Sam Ratulangi Manado, dengan ini menyatakan telah mengadakan pekerjaan <strong>PEMUNASHAN BARANG DILARANG (PROHIBITED ITEMS)</strong> berdasarkan:
            </p>
        </div>

        {{-- Dasar Hukum --}}
        <div class="regulations">
            <ol>
                <li>Peraturan Menteri Perhubungan Nomor PM 80 Tahun 2017 tentang Program Keamanan Penerbangan Nasional.</li>
                <li>Prosedur Operasional Standar (SOP) Penanganan Barang Dilarang dan Barang Sitaan.</li>
            </ol>
        </div>

        <p style="margin: 12px 0;">
            Adapun rincian barang yang dimusnahkan adalah sebagai berikut:
        </p>

        {{-- Tabel Barang --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Barang</th>
                    <th style="width: 20%;">Kategori Barang</th>
                    <th style="width: 25%;">Pemilik Barang</th>
                    <th style="width: 30%;">Metode Pemusnahan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $disposalRecord->item->item_name }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $disposalRecord->item->category)) }}</td>
                    <td>{{ $disposalRecord->item->passenger->full_name }}</td>
                    <td>
                        @switch($disposalRecord->disposal_method)
                            @case('destroyed')
                                Dihancurkan (Destroyed)
                                @break
                            @case('handed_to_police')
                                Diserahkan ke Polisi
                                @break
                            @case('other')
                                Lainnya
                                @break
                            @default
                                {{ $disposalRecord->disposal_method }}
                        @endswitch
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Pelaksanaan Pemusnahan --}}
        <p style="margin: 12px 0;">
            <strong>Pelaksanaan Pemunashan :</strong>
        </p>

        <div class="execution-section">
            <ol>
                <li>Pemusnahan dilakukan dengan cara dirusak/dihancurkan (destroyed) sehingga barang tidak dapat dipergunakan kembali sebagaimana fungsinya.</li>
                <li>Pelaksanaan pemusnahan disaksikan oleh petugas keamanan (AVSEC) yang bertugas.</li>
            </ol>
        </div>

        {{-- Penutup --}}
        <div class="closing">
            Demikian Berita Acara ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya sebagai bukti pertanggungjawaban pengelolaan barang sitaan/tertahan.
        </div>

        {{-- Tanda Tangan --}}
        <div class="signature-section">
            <div class="signature-label">Disetujui Oleh:</div>
            
            <div class="signature-name">{{ strtoupper($disposalRecord->authorizedBy->name) }}</div>
            <div class="signature-title">AVIATION SECURITY DEPARTMENT HEAD</div>
            <div class="signature-title">DEPARTMENT HEAD</div>
        </div>
    </div>
</body>
</html>