"""
API Router - PKBM FastAPI v1
"""

from fastapi import APIRouter
from app.api.v1.endpoints import (
    raport,
    sertifikat,
    analitik,
    laporan,
    prediksi,
    qrcode,
    face_attendance,
)

api_router = APIRouter()

# ================================
# Raport Generation
# ================================
api_router.include_router(
    raport.router,
    prefix="/raport",
    tags=["📄 Raport"],
)

# ================================
# Sertifikat Generation
# ================================
api_router.include_router(
    sertifikat.router,
    prefix="/sertifikat",
    tags=["🎓 Sertifikat"],
)

# ================================
# Analitik & Dashboard
# ================================
api_router.include_router(
    analitik.router,
    prefix="/analitik",
    tags=["📊 Analitik"],
)

# ================================
# Laporan Dinas
# ================================
api_router.include_router(
    laporan.router,
    prefix="/laporan",
    tags=["📋 Laporan"],
)

# ================================
# Prediksi ML
# ================================
api_router.include_router(
    prediksi.router,
    prefix="/prediksi",
    tags=["🤖 Prediksi ML"],
)

# ================================
# QR Code Generator
# ================================
api_router.include_router(
    qrcode.router,
    prefix="/qrcode",
    tags=["🔲 QR Code"],
)

# ================================
# Presensi Biometrik Wajah
# ================================
api_router.include_router(
    face_attendance.router,
    prefix="/face",
    tags=["👤 Presensi Wajah"],
)

