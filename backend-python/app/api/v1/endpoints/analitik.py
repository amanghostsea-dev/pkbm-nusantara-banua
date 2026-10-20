"""
Analitik & Dashboard Data Service
Menyediakan data statistik untuk dashboard PKBM
"""

# pyrefly: ignore [missing-import]
from fastapi import APIRouter, HTTPException, Query
from typing import Optional
from datetime import datetime, timezone

from app.services.analitik_service import AnalitikService

router = APIRouter()
analitik_service = AnalitikService()


@router.get("/dashboard/ringkasan")
async def get_ringkasan_dashboard(tahun_ajaran: Optional[str] = None):
    """
    Data ringkasan untuk dashboard utama:
    - Total siswa aktif per program
    - Rata-rata kehadiran
    - Rata-rata nilai
    - Total lulus bulan ini
    """
    data = await analitik_service.get_ringkasan(tahun_ajaran)
    return data


@router.get("/siswa/distribusi")
async def get_distribusi_siswa(tahun_ajaran: Optional[str] = None):
    """Distribusi siswa per program, per jenis kelamin, per usia"""
    return await analitik_service.get_distribusi_siswa(tahun_ajaran)


@router.get("/nilai/statistik")
async def get_statistik_nilai(
    program: Optional[str] = None,
    semester: Optional[int] = None,
    tahun_ajaran: Optional[str] = None,
):
    """Statistik nilai: rata-rata, min, max, distribusi per mapel"""
    return await analitik_service.get_statistik_nilai(program, semester, tahun_ajaran)


@router.get("/absensi/bulanan")
async def get_absensi_bulanan(
    tahun: int = Query(default=datetime.now(timezone.utc).year),
    program: Optional[str] = None,
):
    """Rekap absensi per bulan dalam setahun"""
    return await analitik_service.get_absensi_bulanan(tahun, program)


@router.get("/kelulusan/historis")
async def get_historis_kelulusan(tahun_mulai: Optional[int] = None):
    """Historis tingkat kelulusan per tahun per program"""
    return await analitik_service.get_historis_kelulusan(tahun_mulai)


@router.get("/pembayaran/rekap")
async def get_rekap_pembayaran(
    bulan: Optional[int] = None,
    tahun: Optional[int] = None,
):
    """Rekap status pembayaran SPP & administrasi"""
    return await analitik_service.get_rekap_pembayaran(bulan, tahun)


@router.get("/performa/kelas/{kelas_id}")
async def get_performa_kelas(kelas_id: int, semester: Optional[int] = None):
    """Analisis performa keseluruhan satu kelas"""
    return await analitik_service.get_performa_kelas(kelas_id, semester)
