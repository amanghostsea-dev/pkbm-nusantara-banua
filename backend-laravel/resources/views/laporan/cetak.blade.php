<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — {{ $nama_pkbm }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #f3f4f6;
            padding: 24px;
            font-size: 12pt;
            line-height: 1.4;
        }
        .page {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 20mm 20mm 25mm 20mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
        }
        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e293b;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            font-size: 13px;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-outline {
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.2);
        }

        /* KOP SURAT */
        .kop-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            padding-bottom: 12px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }
        .kop-logo {
            position: absolute;
            left: 0;
            top: 2px;
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .kop-text {
            width: 100%;
            padding: 0 60px;
        }
        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-nama-sekolah {
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }
        .kop-npsn {
            font-size: 10pt;
            font-weight: 600;
        }
        .kop-kontak {
            font-size: 9pt;
            margin-top: 2px;
            color: #374151;
        }

        /* JUDUL LAPORAN */
        .laporan-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .laporan-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .laporan-title p {
            font-size: 10pt;
            color: #4b5563;
        }

        /* METADATA */
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* SUMMARY BOX */
        .summary-box {
            border: 1px dashed #6b7280;
            padding: 10px 14px;
            background: #f9fafb;
            margin-bottom: 30px;
            font-size: 9.5pt;
        }
        .summary-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-size: 10pt;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-box {
            text-align: center;
            min-width: 220px;
            font-size: 11pt;
        }
        .ttd-space {
            height: 70px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* PRINT MEDIA STYLES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 10mm 15mm 15mm 15mm;
                max-width: 100%;
                width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Hidden on print) -->
    <div class="no-print-bar">
        <div>
            <strong>Mode Cetak Laporan Resmi</strong> — {{ $title }}
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="javascript:window.history.back()" class="btn btn-outline">
                ← Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official Paper Sheet -->
    <div class="page">
        <!-- KOP SURAT DINAS -->
        <div class="kop-wrapper">
            <div class="kop-text">
                <div class="kop-instansi">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
                <div class="kop-nama-sekolah">{{ $nama_pkbm }}</div>
                <div class="kop-npsn">PUSAT KEGIATAN BELAJAR MASYARAKAT (PKBM) • NPSN: {{ $npsn }}</div>
                <div class="kop-kontak">{{ $alamat }} | Telp: {{ $no_telepon }} | Email: {{ $email }}</div>
            </div>
        </div>

        <!-- JUDUL LAPORAN -->
        <div class="laporan-title">
            <h2>{{ $title }}</h2>
            <p>Periode Tahun Ajaran: {{ $tahun_ajaran }} | Program: {{ $nama_program }}</p>
        </div>

        <!-- TABEL DATA -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 100px;">NISN</th>
                    <th>Nama Lengkap Warga Belajar</th>
                    <th style="width: 45px;">L/P</th>
                    <th>Program & Kelas</th>
                    <th style="width: 85px;">Kehadiran</th>
                    <th style="width: 75px;">Rata Nilai</th>
                    <th style="width: 75px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $idx => $s)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-mono">{{ $s['nisn'] }}</td>
                        <td><strong>{{ $s['nama_lengkap'] }}</strong></td>
                        <td class="text-center">{{ $s['jenis_kelamin'] }}</td>
                        <td>{{ $s['program'] }}</td>
                        <td class="text-center">{{ $s['kehadiran'] }}%</td>
                        <td class="text-center">{{ $s['nilai'] }}</td>
                        <td class="text-center">
                            <span class="font-bold">{{ $s['status'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px;">
                            Tidak ada data warga belajar untuk kriteria yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- RINGKASAN REKAPITULASI -->
        <div class="summary-box">
            <div class="summary-title">Ringkasan Statistik Eksekutif:</div>
            <table style="width: 100%; border: none; font-size: 9.5pt;">
                <tr>
                    <td style="width: 25%;">Total Warga Belajar</td>
                    <td style="width: 25%;">: <strong>{{ count($siswaList) }} Orang</strong></td>
                    <td style="width: 25%;">Tingkat Rata-rata Kehadiran</td>
                    <td style="width: 25%;">: <strong>{{ $summary['avg_kehadiran'] }}%</strong></td>
                </tr>
                <tr>
                    <td>Laki-laki (L) / Perempuan (P)</td>
                    <td>: {{ $summary['total_l'] }} L / {{ $summary['total_p'] }} P</td>
                    <td>Rata-rata Nilai Akademik</td>
                    <td>: <strong>{{ $summary['avg_nilai'] }}</strong></td>
                </tr>
                <tr>
                    <td>Status Aktif Belajar</td>
                    <td>: {{ $summary['total_aktif'] }} Siswa Aktif</td>
                    <td>Waktu Pembuatan Dokumen</td>
                    <td>: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        <!-- TANDA TANGAN RESMI -->
        <div class="ttd-wrapper">
            <div class="ttd-box">
                <div>Ditetapkan di: Banua</div>
                <div>Pada tanggal: {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 4px; font-weight: bold;">Kepala {{ $nama_pkbm }}</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $kepala_pkbm }}</div>
                <div style="font-size: 9.5pt; color: #4b5563;">NPSN: {{ $npsn }}</div>
            </div>
        </div>
    </div>

</body>
</html>
