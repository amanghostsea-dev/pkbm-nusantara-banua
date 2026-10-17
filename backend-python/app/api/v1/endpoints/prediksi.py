"""
ML Prediksi Kelulusan Siswa
Menggunakan scikit-learn untuk memprediksi kemungkinan kelulusan siswa
berdasarkan data historis nilai, absensi, dan profil siswa
"""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import numpy as np

from app.ml.kelulusan_predictor import KelulusanPredictor
from app.services.analitik_service import AnalitikService

router = APIRouter()
predictor = KelulusanPredictor()
analitik_service = AnalitikService()


# ==============================
# Schemas
# ==============================

class SiswaPrediksiRequest(BaseModel):
    siswa_id: int
    rata_rata_nilai: float
    persentase_kehadiran: float
    nilai_matematika: Optional[float] = None
    nilai_bahasa_indonesia: Optional[float] = None
    nilai_ipa: Optional[float] = None
    jumlah_semester_sudah_ditempuh: int
    status_pembayaran_lancar: bool
    usia: int
    jenis_kelamin: str  # L | P


class BulkPrediksiRequest(BaseModel):
    kelas_id: int
    semester: int


class PrediksiResult(BaseModel):
    siswa_id: int
    probabilitas_lulus: float
    prediksi: str  # "LULUS" | "TIDAK LULUS" | "PERLU PERHATIAN"
    confidence: str  # "TINGGI" | "SEDANG" | "RENDAH"
    faktor_risiko: List[str]
    rekomendasi: List[str]
    skor_detail: Dict[str, float]


# ==============================
# Endpoints
# ==============================

@router.post("/kelulusan", response_model=PrediksiResult)
async def prediksi_kelulusan_siswa(request: SiswaPrediksiRequest):
    """
    Prediksi kemungkinan kelulusan siswa menggunakan Machine Learning
    
    Model menganalisis:
    - Rata-rata nilai akademik
    - Persentase kehadiran
    - Nilai mata pelajaran utama
    - Progress semester
    - Faktor sosial
    """
    try:
        result = await predictor.predict_single(request)
        return result
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Prediksi gagal: {str(e)}"
        )


@router.post("/kelulusan/bulk")
async def prediksi_kelulusan_kelas(request: BulkPrediksiRequest):
    """Prediksi kelulusan seluruh siswa dalam satu kelas"""
    try:
        results = await predictor.predict_kelas(
            kelas_id=request.kelas_id,
            semester=request.semester,
        )
        return {
            "kelas_id": request.kelas_id,
            "total_siswa": len(results),
            "prediksi_lulus": sum(1 for r in results if r["prediksi"] == "LULUS"),
            "prediksi_tidak_lulus": sum(1 for r in results if r["prediksi"] == "TIDAK LULUS"),
            "perlu_perhatian": sum(1 for r in results if r["prediksi"] == "PERLU PERHATIAN"),
            "results": results,
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/model/info")
async def get_model_info():
    """Informasi tentang ML model yang digunakan"""
    return await predictor.get_model_info()


@router.post("/model/retrain")
async def retrain_model():
    """
    Re-train ML model dengan data terbaru dari database
    (Hanya bisa dijalankan oleh admin)
    """
    try:
        result = await predictor.retrain()
        return {
            "success": True,
            "message": "Model berhasil di-retrain",
            "accuracy": result["accuracy"],
            "training_samples": result["training_samples"],
            "trained_at": result["trained_at"],
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Retrain gagal: {str(e)}")


@router.get("/tren-nilai/{program}")
async def analisis_tren_nilai(program: str, tahun: Optional[int] = None):
    """
    Analisis tren nilai per program (paket_a/b/c)
    Menampilkan grafik tren naik/turun performa akademik
    """
    try:
        data = await analitik_service.get_tren_nilai(program, tahun)
        return data
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/risiko-dropout/{kelas_id}")
async def identifikasi_risiko_dropout(kelas_id: int):
    """
    Identifikasi siswa dengan risiko dropout tinggi
    Berdasarkan pola kehadiran, nilai, dan pembayaran
    """
    try:
        siswa_berisiko = await predictor.identify_dropout_risk(kelas_id)
        return {
            "kelas_id": kelas_id,
            "total_berisiko": len(siswa_berisiko),
            "siswa_berisiko": siswa_berisiko,
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
