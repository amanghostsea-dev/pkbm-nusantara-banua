"""
Sertifikat PDF Generator Endpoint
Generate sertifikat kelulusan resmi PKBM
"""

from fastapi import APIRouter, HTTPException
from fastapi.responses import FileResponse
from pydantic import BaseModel
from typing import Optional
import os

from app.services.sertifikat_service import SertifikatService
from app.core.config import settings

router = APIRouter()
sertifikat_service = SertifikatService()


class SertifikatRequest(BaseModel):
    siswa_id: int
    program_id: int
    nama_siswa: str
    nisn: str
    tempat_lahir: str
    tanggal_lahir: str
    program: str
    nomor_sertifikat: str
    tanggal_terbit: str
    tahun_lulus: str
    kepala_pkbm: str
    nama_pkbm: str
    npsn: str
    nilai_akhir: float
    predikat: str


@router.post("/generate")
async def generate_sertifikat(request: SertifikatRequest):
    """Generate sertifikat kelulusan PDF"""
    try:
        file_name = f"sertifikat_{request.siswa_id}_{request.program}_{request.tahun_lulus}.pdf"
        file_path = os.path.join(settings.SERTIFIKAT_PATH, file_name)
        
        await sertifikat_service.generate_pdf(request, file_path)
        
        return {
            "success": True,
            "message": "Sertifikat berhasil dibuat",
            "file_path": file_path,
            "download_url": f"/api/v1/sertifikat/download/{file_name}",
            "nomor_sertifikat": request.nomor_sertifikat,
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Gagal membuat sertifikat: {str(e)}")


@router.get("/download/{filename}")
async def download_sertifikat(filename: str):
    """Download file sertifikat PDF"""
    file_path = os.path.join(settings.SERTIFIKAT_PATH, filename)
    
    if not os.path.exists(file_path):
        raise HTTPException(status_code=404, detail="File sertifikat tidak ditemukan")
    
    return FileResponse(
        path=file_path,
        filename=filename,
        media_type="application/pdf",
    )


@router.get("/verify/{nomor_sertifikat}")
async def verify_sertifikat(nomor_sertifikat: str):
    """Verifikasi keaslian sertifikat berdasarkan nomor"""
    result = await sertifikat_service.verify(nomor_sertifikat)
    return result
