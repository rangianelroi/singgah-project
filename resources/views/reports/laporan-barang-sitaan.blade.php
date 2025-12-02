<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Sitaan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Barang Sitaan</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Penumpang</th>
                <th>Penerbangan</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->confiscation_date)->format('d/m/Y') }}</td>
                <td>{{ $item->passenger->full_name ?? '-' }}</td>
                <td>{{ $item->flight->flight_number ?? '-' }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
        <p>Manado, {{ now()->format('d F Y') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>Department Head AVSEC</strong></p>
    </div>
</body>
</html>