<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pemusnahan</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .content { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .footer { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA PEMUSNAHAN BARANG</h1>
        <p>Nomor: BA/{{ $disposalRecord->id }}/{{ $disposalRecord->disposal_date->format('m/Y') }}</p>
    </div>

    <div class="content">
        <p>Pada hari ini, {{ $disposalRecord->disposal_date->format('l, d F Y') }}, telah dilakukan pemusnahan barang sitaan dengan rincian sebagai berikut:</p>
        
        <table>
            <tr>
                <th style="width: 30%;">Nama Barang</th>
                <td>{{ $disposalRecord->item->item_name }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ $disposalRecord->item->category }}</td>
            </tr>
            <tr>
                <th>Pemilik Barang</th>
                <td>{{ $disposalRecord->item->passenger->full_name }}</td>
            </tr>
            <tr>
                <th>Metode Pemusnahan</th>
                <td>{{ $disposalRecord->disposal_method }}</td>
            </tr>
            <tr>
                <th>Saksi-saksi</th>
                <td>{{ $disposalRecord->witnesses ?? '-' }}</td>
            </tr>
        </table>

        <p>Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer">
        <p>Dibuat di Manado, {{ $disposalRecord->disposal_date->format('d F Y') }}</p>
        <br><br><br>
        <p><strong><u>{{ $disposalRecord->authorizedBy->name }}</u></strong></p>
        <p><em>{{ $disposalRecord->authorizedBy->role }}</em></p>
    </div>
</body>
</html>