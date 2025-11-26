<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pemusnahan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .page {
            background: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 50px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid #ef4444;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #6b7280;
            font-size: 13px;
            margin: 4px 0;
        }

        .document-number {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .intro {
            font-size: 14px;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9fafb;
            border-left: 4px solid #ef4444;
            border-radius: 4px;
        }

        .content-section {
            margin-bottom: 30px;
        }

        .content-section h2 {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .item-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .item-card-header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 13px;
        }

        .item-card-content {
            padding: 16px;
        }

        .item-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 16px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .item-row:last-child {
            margin-bottom: 0;
        }

        .item-label {
            font-weight: 600;
            color: #6b7280;
            text-transform: capitalize;
        }

        .item-value {
            color: #1f2937;
            word-break: break-word;
        }

        .item-value.badge {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .closure {
            font-size: 13px;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 30px;
            padding: 20px;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 4px;
            text-align: center;
        }

        .signature-section {
            margin-top: 60px;
        }

        .signature-section h3 {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            width: 200px;
            margin: 40px auto 8px;
        }

        .signature-name {
            font-weight: 600;
            font-size: 13px;
            color: #1f2937;
        }

        .signature-title {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            font-style: italic;
        }

        .footer {
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
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
                padding: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Header Section --}}
        <div class="header">
            <h1>🗑️ BERITA ACARA PEMUSNAHAN BARANG</h1>
            <p>Laporan Resmi Eksekusi Pemusnahan Barang Sitaan</p>
            <div class="document-number">
                <strong>Nomor:</strong> BA/{{ $disposalRecord->id }}/{{ $disposalRecord->disposal_date->format('m/Y') }}
            </div>
        </div>

        {{-- Introduction --}}
        <div class="intro">
            <strong>📋 Pemberitahuan:</strong> Pada tanggal <strong>{{ $disposalRecord->disposal_date->format('d F Y') }}</strong>, 
            telah dilaksanakan pemusnahan barang sitaan di bandara Sam Ratulangi Manado sesuai dengan prosedur yang berlaku. 
            Berita acara ini adalah dokumen resmi sebagai bukti pelaksanaan kegiatan tersebut.
        </div>

        {{-- Content Section --}}
        <div class="content-section">
            <h2>📦 Rincian Barang yang Dimusnahkan</h2>
            
            <div class="item-card">
                <div class="item-card-header">
                    Informasi Barang Sitaan
                </div>
                <div class="item-card-content">
                    <div class="item-row">
                        <div class="item-label">Nama Barang</div>
                        <div class="item-value">{{ $disposalRecord->item->item_name }}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Kategori</div>
                        <div class="item-value badge">{{ str_replace('_', ' ', Str::title(strtolower($disposalRecord->item->category))) }}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Pemilik Barang</div>
                        <div class="item-value">{{ $disposalRecord->item->passenger->full_name }}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Nomor ID Barang</div>
                        <div class="item-value">#{{ $disposalRecord->item->id }}</div>
                    </div>
                </div>
            </div>

            <div class="item-card">
                <div class="item-card-header">
                    Detail Pemusnahan
                </div>
                <div class="item-card-content">
                    <div class="item-row">
                        <div class="item-label">Metode Pemusnahan</div>
                        <div class="item-value">
                            @switch($disposalRecord->disposal_method)
                                @case('destroyed')
                                    🔥 Dimusnahkan
                                    @break
                                @case('handed_to_police')
                                    🚓 Diserahkan ke Polisi
                                    @break
                                @case('other')
                                    📋 Lainnya
                                    @break
                                @default
                                    {{ $disposalRecord->disposal_method }}
                            @endswitch
                        </div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Tanggal Eksekusi</div>
                        <div class="item-value">{{ $disposalRecord->disposal_date->format('d F Y') }}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Saksi-Saksi</div>
                        <div class="item-value">{{ $disposalRecord->witnesses ?? '—' }}</div>
                    </div>
                    <div class="item-row">
                        <div class="item-label">Petugas Eksekusi</div>
                        <div class="item-value">{{ $disposalRecord->authorizedBy->name }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Closure --}}
        <div class="closure">
            Demikian Berita Acara Pemusnahan Barang ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya 
            dan menjadi arsip resmi Direktorat Keamanan Bandara (AVSEC) Sam Ratulangi Manado.
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <h3>Tanda Tangan & Persetujuan</h3>
            
            <div class="signature-grid">
                <div class="signature-block">
                    <p style="font-size: 12px; color: #6b7280; margin-bottom: 30px;">Dibuat oleh,</p>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $disposalRecord->authorizedBy->name }}</div>
                    <div class="signature-title">{{ strtoupper($disposalRecord->authorizedBy->role) }}</div>
                </div>
                
                <div class="signature-block">
                    <p style="font-size: 12px; color: #6b7280; margin-bottom: 30px;">Disetujui oleh,</p>
                    <div class="signature-line"></div>
                    <div class="signature-name">___________________</div>
                    <div class="signature-title">Kepala Bagian AVSEC</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Manado, {{ $disposalRecord->disposal_date->format('d F Y') }}</p>
            <p style="margin-top: 8px;">Dokumen ini adalah arsip resmi dan memiliki nilai legal yang sama dengan dokumen asli.</p>
        </div>
    </div>
</body>
</html>