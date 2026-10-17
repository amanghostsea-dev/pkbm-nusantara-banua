"""
Raport PDF Service
Menggunakan ReportLab untuk generate raport PDF profesional
"""

import os
import asyncio
from pathlib import Path
from typing import Optional, List
from datetime import datetime
import json

from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import cm, mm
from reportlab.lib.colors import HexColor, black, white
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Table, TableStyle,
    Spacer, Image, HRFlowable, PageBreak
)
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT, TA_JUSTIFY
from reportlab.pdfgen import canvas
from reportlab.lib import colors

from app.core.config import settings


class RaportService:
    """Service untuk generate raport PDF siswa PKBM"""
    
    # Warna tema PKBM
    COLOR_PRIMARY = HexColor('#1a5276')    # Biru tua
    COLOR_SECONDARY = HexColor('#2e86c1')  # Biru
    COLOR_ACCENT = HexColor('#f39c12')     # Kuning/emas
    COLOR_SUCCESS = HexColor('#27ae60')    # Hijau (lulus)
    COLOR_DANGER = HexColor('#e74c3c')     # Merah (tidak lulus)
    COLOR_LIGHT = HexColor('#ebf5fb')      # Biru muda (bg header tabel)
    COLOR_GRAY = HexColor('#7f8c8d')       # Abu-abu
    
    PROGRAM_LABELS = {
        'paket_a': 'Paket A (Setara SD)',
        'paket_b': 'Paket B (Setara SMP)',
        'paket_c': 'Paket C (Setara SMA)',
    }
    
    def __init__(self):
        os.makedirs(settings.RAPORT_PATH, exist_ok=True)
        os.makedirs(settings.TEMP_PATH, exist_ok=True)
    
    async def generate_pdf(self, data, output_path: str) -> str:
        """Generate raport PDF untuk satu siswa"""
        
        # Jalankan di thread pool (blocking I/O)
        loop = asyncio.get_event_loop()
        await loop.run_in_executor(None, self._build_pdf, data, output_path)
        return output_path
    
    def _build_pdf(self, data, output_path: str):
        """Build PDF document (sync)"""
        doc = SimpleDocTemplate(
            output_path,
            pagesize=A4,
            rightMargin=1.5*cm,
            leftMargin=1.5*cm,
            topMargin=1.5*cm,
            bottomMargin=2*cm,
        )
        
        styles = getSampleStyleSheet()
        story = []
        
        # ===========================
        # 1. HEADER RAPORT
        # ===========================
        story.extend(self._build_header(data, styles))
        story.append(Spacer(1, 0.3*cm))
        
        # ===========================
        # 2. DATA SISWA
        # ===========================
        story.extend(self._build_data_siswa(data, styles))
        story.append(Spacer(1, 0.4*cm))
        
        # ===========================
        # 3. TABEL NILAI
        # ===========================
        story.extend(self._build_tabel_nilai(data, styles))
        story.append(Spacer(1, 0.4*cm))
        
        # ===========================
        # 4. REKAP ABSENSI
        # ===========================
        story.extend(self._build_absensi(data, styles))
        story.append(Spacer(1, 0.4*cm))
        
        # ===========================
        # 5. CATATAN & TANDA TANGAN
        # ===========================
        story.extend(self._build_footer(data, styles))
        
        doc.build(story)
    
    def _build_header(self, data, styles):
        """Build bagian header raport"""
        elements = []
        
        header_style = ParagraphStyle(
            'Header',
            parent=styles['Normal'],
            fontSize=14,
            fontName='Helvetica-Bold',
            textColor=self.COLOR_PRIMARY,
            alignment=TA_CENTER,
            spaceAfter=2,
        )
        
        sub_header_style = ParagraphStyle(
            'SubHeader',
            parent=styles['Normal'],
            fontSize=11,
            fontName='Helvetica-Bold',
            textColor=self.COLOR_PRIMARY,
            alignment=TA_CENTER,
            spaceAfter=2,
        )
        
        normal_center = ParagraphStyle(
            'NormalCenter',
            parent=styles['Normal'],
            fontSize=10,
            alignment=TA_CENTER,
        )
        
        # Judul Raport
        elements.append(Paragraph("LAPORAN HASIL BELAJAR PESERTA DIDIK", header_style))
        elements.append(Paragraph(
            f"PENDIDIKAN KESETARAAN {self.PROGRAM_LABELS.get(data.program, data.program).upper()}",
            sub_header_style
        ))
        elements.append(Spacer(1, 0.2*cm))
        
        # Info PKBM dalam tabel
        info_data = [
            [
                Paragraph(f"<b>{data.nama_pkbm}</b>", normal_center),
            ],
            [
                Paragraph(f"{data.alamat_pkbm}", normal_center),
            ],
            [
                Paragraph(f"NPSN: {data.npsn}", normal_center),
            ],
        ]
        
        info_table = Table(info_data, colWidths=[17*cm])
        info_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), self.COLOR_PRIMARY),
            ('TEXTCOLOR', (0, 0), (-1, 0), white),
            ('BACKGROUND', (0, 1), (-1, -1), self.COLOR_LIGHT),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, -1), 10),
            ('PADDING', (0, 0), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, self.COLOR_SECONDARY),
        ]))
        
        elements.append(info_table)
        elements.append(HRFlowable(
            width="100%",
            thickness=2,
            color=self.COLOR_ACCENT,
            spaceAfter=0.2*cm,
        ))
        
        return elements
    
    def _build_data_siswa(self, data, styles):
        """Build bagian data identitas siswa"""
        elements = []
        
        label_style = ParagraphStyle(
            'Label', parent=styles['Normal'],
            fontSize=9, fontName='Helvetica-Bold',
        )
        value_style = ParagraphStyle(
            'Value', parent=styles['Normal'],
            fontSize=9,
        )
        
        title_style = ParagraphStyle(
            'Title', parent=styles['Normal'],
            fontSize=10, fontName='Helvetica-Bold',
            textColor=self.COLOR_PRIMARY,
            spaceAfter=4,
        )
        
        elements.append(Paragraph("IDENTITAS PESERTA DIDIK", title_style))
        
        # Format tanggal lahir
        try:
            tgl = datetime.strptime(data.tanggal_lahir, '%Y-%m-%d')
            tgl_str = tgl.strftime('%d %B %Y')
        except:
            tgl_str = data.tanggal_lahir
        
        jk = "Laki-laki" if data.jenis_kelamin == "L" else "Perempuan"
        
        data_siswa = [
            ["Nama Siswa", ":", data.nama_siswa, "Semester", ":", str(data.semester)],
            ["NISN", ":", data.nisn, "Tahun Ajaran", ":", data.tahun_ajaran],
            ["Tempat, Tgl Lahir", ":", f"{data.tempat_lahir}, {tgl_str}", "Program", ":", self.PROGRAM_LABELS.get(data.program, data.program)],
            ["Jenis Kelamin", ":", jk, "Kelas", ":", data.kelas],
            ["Nama Orang Tua", ":", data.nama_orang_tua, "Wali Kelas", ":", data.wali_kelas],
        ]
        
        table = Table(
            data_siswa,
            colWidths=[3.5*cm, 0.5*cm, 5.5*cm, 2.5*cm, 0.5*cm, 4.5*cm]
        )
        table.setStyle(TableStyle([
            ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
            ('FONTNAME', (3, 0), (3, -1), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, -1), 9),
            ('PADDING', (0, 0), (-1, -1), 4),
            ('BACKGROUND', (0, 0), (-1, -1), self.COLOR_LIGHT),
            ('GRID', (0, 0), (-1, -1), 0.3, self.COLOR_GRAY),
        ]))
        
        elements.append(table)
        return elements
    
    def _build_tabel_nilai(self, data, styles):
        """Build tabel nilai mata pelajaran"""
        elements = []
        
        title_style = ParagraphStyle(
            'Title', parent=styles['Normal'],
            fontSize=10, fontName='Helvetica-Bold',
            textColor=self.COLOR_PRIMARY,
            spaceAfter=4,
        )
        
        elements.append(Paragraph("NILAI AKADEMIK", title_style))
        
        # Header tabel
        header = [
            Paragraph("<b>No</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>Mata Pelajaran</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>Tugas</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>UTS</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>UAS</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>Praktek</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>Akhir</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>KKM</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
            Paragraph("<b>Pred.</b>", ParagraphStyle('h', fontSize=8, alignment=TA_CENTER, fontName='Helvetica-Bold')),
        ]
        
        table_data = [header]
        
        cell_style = ParagraphStyle('cell', fontSize=8, alignment=TA_CENTER)
        cell_left = ParagraphStyle('cellL', fontSize=8, alignment=TA_LEFT)
        
        for i, nilai in enumerate(data.nilai_list, 1):
            warna_nilai = self.COLOR_SUCCESS if nilai.lulus else self.COLOR_DANGER
            
            row = [
                Paragraph(str(i), cell_style),
                Paragraph(nilai.nama_mapel, cell_left),
                Paragraph(f"{nilai.nilai_tugas:.0f}", cell_style),
                Paragraph(f"{nilai.nilai_uts:.0f}", cell_style),
                Paragraph(f"{nilai.nilai_uas:.0f}", cell_style),
                Paragraph(f"{nilai.nilai_praktek:.0f}" if nilai.nilai_praktek else "-", cell_style),
                Paragraph(f"<b>{nilai.nilai_akhir:.0f}</b>", cell_style),
                Paragraph(str(nilai.kkm), cell_style),
                Paragraph(f"<b>{nilai.predikat}</b>", cell_style),
            ]
            table_data.append(row)
        
        # Baris rata-rata
        rata_row = [
            "", 
            Paragraph("<b>RATA-RATA</b>", ParagraphStyle('r', fontSize=8, fontName='Helvetica-Bold')),
            "", "", "", "",
            Paragraph(f"<b>{data.rata_rata:.2f}</b>", cell_style),
            "", "",
        ]
        table_data.append(rata_row)
        
        col_widths = [1*cm, 5*cm, 1.5*cm, 1.5*cm, 1.5*cm, 1.5*cm, 1.5*cm, 1.2*cm, 1.3*cm]
        table = Table(table_data, colWidths=col_widths, repeatRows=1)
        
        # Styling tabel
        style = TableStyle([
            # Header
            ('BACKGROUND', (0, 0), (-1, 0), self.COLOR_PRIMARY),
            ('TEXTCOLOR', (0, 0), (-1, 0), white),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, -1), 8),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            ('PADDING', (0, 0), (-1, -1), 4),
            ('GRID', (0, 0), (-1, -1), 0.3, self.COLOR_GRAY),
            # Alternating rows
            ('ROWBACKGROUNDS', (0, 1), (-1, -2), [white, self.COLOR_LIGHT]),
            # Rata-rata row
            ('BACKGROUND', (0, -1), (-1, -1), self.COLOR_ACCENT),
            ('FONTNAME', (0, -1), (-1, -1), 'Helvetica-Bold'),
            ('SPAN', (0, -1), (0, -1)),
        ])
        
        # Warna merah untuk nilai tidak lulus
        for i, nilai in enumerate(data.nilai_list, 1):
            if not nilai.lulus:
                style.add('TEXTCOLOR', (6, i), (6, i), self.COLOR_DANGER)
                style.add('FONTNAME', (6, i), (6, i), 'Helvetica-Bold')
        
        table.setStyle(style)
        elements.append(table)
        return elements
    
    def _build_absensi(self, data, styles):
        """Build bagian rekap absensi"""
        elements = []
        
        title_style = ParagraphStyle(
            'Title', parent=styles['Normal'],
            fontSize=10, fontName='Helvetica-Bold',
            textColor=self.COLOR_PRIMARY,
            spaceAfter=4,
        )
        
        elements.append(Paragraph("REKAP KEHADIRAN", title_style))
        
        absensi = data.absensi
        total = absensi.total_hadir + absensi.total_sakit + absensi.total_izin + absensi.total_alpha
        
        absensi_data = [
            ["Hadir", "Sakit", "Izin", "Alpha", "Total", "% Kehadiran"],
            [
                str(absensi.total_hadir),
                str(absensi.total_sakit),
                str(absensi.total_izin),
                str(absensi.total_alpha),
                str(total),
                f"{absensi.persentase_hadir:.1f}%",
            ],
        ]
        
        cell_s = ParagraphStyle('cs', fontSize=9, alignment=TA_CENTER)
        
        table = Table(absensi_data, colWidths=[2.5*cm]*6)
        table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), self.COLOR_SECONDARY),
            ('TEXTCOLOR', (0, 0), (-1, 0), white),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTSIZE', (0, 0), (-1, -1), 9),
            ('PADDING', (0, 0), (-1, -1), 5),
            ('GRID', (0, 0), (-1, -1), 0.3, self.COLOR_GRAY),
            ('BACKGROUND', (0, 1), (-1, 1), self.COLOR_LIGHT),
        ]))
        elements.append(table)
        
        # Status naik/tinggal
        status_map = {
            'naik': ('NAIK KELAS', self.COLOR_SUCCESS),
            'tinggal': ('TINGGAL KELAS', self.COLOR_DANGER),
            'lulus': ('LULUS', self.COLOR_SUCCESS),
        }
        status_text, status_color = status_map.get(data.status_naik, ('N/A', self.COLOR_GRAY))
        
        elements.append(Spacer(1, 0.3*cm))
        elements.append(Paragraph(
            f"<b>Status: <font color='#{status_color.hexval()[2:].upper()}'>{status_text}</font> | Peringkat ke-{data.peringkat}</b>",
            ParagraphStyle('status', fontSize=10, alignment=TA_CENTER, fontName='Helvetica-Bold'),
        ))
        
        return elements
    
    def _build_footer(self, data, styles):
        """Build bagian catatan dan tanda tangan"""
        elements = []
        elements.append(Spacer(1, 0.3*cm))
        
        catatan_style = ParagraphStyle(
            'Catatan', parent=styles['Normal'],
            fontSize=9, borderPad=5,
        )
        
        catatan = data.catatan_wali_kelas or "Pertahankan dan tingkatkan prestasi belajar Anda."
        elements.append(Paragraph(f"<b>Catatan Wali Kelas:</b>", catatan_style))
        elements.append(Paragraph(catatan, catatan_style))
        elements.append(Spacer(1, 0.5*cm))
        
        # Tanda tangan
        ttd_data = [
            [
                f"Mengetahui,\nOrang Tua / Wali",
                "",
                f"Mengetahui,\nKepala PKBM",
                "",
                f"{data.kelas}, ......................\nWali Kelas",
            ],
            ["\n\n\n\n", "", "\n\n\n\n", "", "\n\n\n\n"],
            [
                "( ................................ )",
                "",
                f"( {data.kepala_pkbm} )",
                "",
                f"( {data.wali_kelas} )",
            ],
        ]
        
        ttd_table = Table(ttd_data, colWidths=[4*cm, 1*cm, 4*cm, 1*cm, 4*cm])
        ttd_table.setStyle(TableStyle([
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTSIZE', (0, 0), (-1, -1), 8),
            ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
            ('PADDING', (0, 0), (-1, -1), 3),
        ]))
        
        elements.append(ttd_table)
        return elements
    
    async def generate_bulk(self, job_id: str, kelas_id: int, semester: int,
                            tahun_ajaran: str, siswa_ids: Optional[List[int]] = None):
        """Generate raport untuk seluruh kelas (background task)"""
        # Implementation untuk bulk generate
        # Akan dipanggil sebagai background task
        pass
    
    async def get_job_status(self, job_id: str):
        """Cek status job generate raport"""
        # Ambil dari Redis
        pass
    
    async def get_preview_data(self, siswa_id: int, semester: int, tahun_ajaran: str):
        """Preview data raport dari Laravel API"""
        pass
