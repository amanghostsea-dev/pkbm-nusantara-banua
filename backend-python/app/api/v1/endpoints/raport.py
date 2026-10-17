"""
Raport PDF Generator Endpoint
Generate raport siswa PKBM dalam format PDF profesional
"""

from fastapi import APIRouter, HTTPException, BackgroundTasks
from fastapi.responses import FileResponse, JSONResponse
from pydantic import BaseModel
from typing import List, Optional
from pathlib import Path
import uuid
import os
from datetime import datetime

from app.services.raport_service import RaportService
from app.core.config import settings

router = APIRouter()
raport_service = RaportService()


# ==============================
# Request/Response Schemas
# ==============================

class NilaiMapel(BaseModel):
    nama_mapel: str
    kode_mapel: str
    nilai_tugas: float
    nilai_uts: float
    nilai_uas: float
    nilai_praktek: Optional[float] = None
    nilai_akhir: float
    kkm: int
    predikat: str
    lulus: bool


class AbsensiData(BaseModel):
    total_hadir: int
    total_sakit: int
    total_izin: int
    total_alpha: int
    persentase_hadir: float


class RaportRequest(BaseModel):
    siswa_id: int
    nama_siswa: str
    nisn: str
    tempat_lahir: str
    tanggal_lahir: str
    jenis_kelamin: str
    nama_orang_tua: str
    program: str  # paket_a | paket_b | paket_c
    angkatan: str
    kelas: str
    semester: int
    tahun_ajaran: str
    wali_kelas: str
    kepala_pkbm: str
    nama_pkbm: str
    alamat_pkbm: str
    npsn: str
    nilai_list: List[NilaiMapel]
    absensi: AbsensiData
    rata_rata: float
    peringkat: int
    status_naik: str  # naik | tinggal | lulus
    catatan_wali_kelas: Optional[str] = None


class BulkRaportRequest(BaseModel):
    kelas_id: int
    semester: int
    tahun_ajaran: str
    siswa_ids: Optional[List[int]] = None  # None = semua siswa


class RaportResponse(BaseModel):
    success: bool
    message: str
    file_path: Optional[str] = None
    download_url: Optional[str] = None
    siswa_id: Optional[int] = None


# ==============================
# Endpoints
# ==============================

@router.post("/generate", response_model=RaportResponse)
async def generate_raport_single(request: RaportRequest):
    """
    Generate raport PDF untuk satu siswa
    
    Returns file path & download URL untuk raport yang sudah dibuat
    """
    try:
        file_name = f"raport_{request.siswa_id}_sem{request.semester}_{request.tahun_ajaran.replace('/', '-')}.pdf"
        file_path = os.path.join(settings.RAPORT_PATH, file_name)
        
        # Generate PDF
        await raport_service.generate_pdf(request, file_path)
        
        return RaportResponse(
            success=True,
            message="Raport berhasil dibuat",
            file_path=file_path,
            download_url=f"/api/v1/raport/download/{file_name}",
            siswa_id=request.siswa_id,
        )
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Gagal membuat raport: {str(e)}")


@router.post("/generate-bulk")
async def generate_raport_bulk(
    request: BulkRaportRequest,
    background_tasks: BackgroundTasks,
):
    """
    Generate raport PDF untuk seluruh kelas (background task)
    
    Proses dijalankan di background, hasilnya di-zip & bisa didownload
    """
    job_id = str(uuid.uuid4())
    
    # Jalankan di background
    background_tasks.add_task(
        raport_service.generate_bulk,
        job_id=job_id,
        kelas_id=request.kelas_id,
        semester=request.semester,
        tahun_ajaran=request.tahun_ajaran,
        siswa_ids=request.siswa_ids,
    )
    
    return {
        "success": True,
        "message": "Proses generate raport dimulai di background",
        "job_id": job_id,
        "status_url": f"/api/v1/raport/status/{job_id}",
    }


@router.get("/status/{job_id}")
async def get_raport_job_status(job_id: str):
    """Cek status progress generate raport bulk"""
    status = await raport_service.get_job_status(job_id)
    if not status:
        raise HTTPException(status_code=404, detail="Job tidak ditemukan")
    return status


@router.get("/download/{filename}")
async def download_raport(filename: str):
    """Download file raport PDF"""
    file_path = os.path.join(settings.RAPORT_PATH, filename)
    
    if not os.path.exists(file_path):
        raise HTTPException(status_code=404, detail="File raport tidak ditemukan")
    
    return FileResponse(
        path=file_path,
        filename=filename,
        media_type="application/pdf",
    )


@router.get("/preview/{siswa_id}/{semester}")
async def preview_raport_data(siswa_id: int, semester: int, tahun_ajaran: str):
    """Preview data raport sebelum di-generate (validasi data)"""
    # Ambil data dari Laravel via internal API
    data = await raport_service.get_preview_data(siswa_id, semester, tahun_ajaran)
    return data
