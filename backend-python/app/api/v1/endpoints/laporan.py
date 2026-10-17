"""
Laporan Dinas — Export Excel & PDF Format Resmi
"""

from fastapi import APIRouter, HTTPException
from fastapi.responses import FileResponse
from pydantic import BaseModel
from typing import Optional
import os
from datetime import datetime

from app.services.laporan_service import LaporanService
from app.core.config import settings

router = APIRouter()
laporan_service = LaporanService()


class LaporanRequest(BaseModel):
    jenis: str  # "bulanan" | "semester" | "tahunan" | "kelulusan"
    bulan: Optional[int] = None
    semester: Optional[int] = None
    tahun: int
    program: Optional[str] = None  # paket_a | paket_b | paket_c | semua
    format: str = "excel"  # "excel" | "pdf"


@router.post("/generate")
async def generate_laporan(request: LaporanRequest):
    """
    Generate laporan format resmi dinas.
    Support format Excel (.xlsx) dan PDF.
    """
    try:
        result = await laporan_service.generate(request)
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/download/{filename}")
async def download_laporan(filename: str):
    """Download file laporan"""
    file_path = os.path.join(settings.TEMP_PATH, filename)
    
    if not os.path.exists(file_path):
        raise HTTPException(status_code=404, detail="File laporan tidak ditemukan")
    
    media_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    if filename.endswith(".pdf"):
        media_type = "application/pdf"
    
    return FileResponse(path=file_path, filename=filename, media_type=media_type)


@router.get("/template/{jenis}")
async def get_template_laporan(jenis: str):
    """Informasi template laporan yang tersedia"""
    templates = {
        "bulanan": "Rekap kehadiran dan nilai bulanan",
        "semester": "Rekap nilai per semester + statistik kelas",
        "tahunan": "Laporan tahunan komprehensif untuk dinas",
        "kelulusan": "Daftar peserta lulus & sertifikat",
        "keuangan": "Rekap administrasi keuangan SPP",
    }
    
    if jenis not in templates:
        raise HTTPException(status_code=404, detail=f"Template '{jenis}' tidak tersedia")
    
    return {
        "jenis": jenis,
        "deskripsi": templates[jenis],
        "format_tersedia": ["excel", "pdf"],
    }
