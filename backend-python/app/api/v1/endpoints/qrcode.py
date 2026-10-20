"""
QR Code Generator untuk Absensi
"""

# pyrefly: ignore [missing-import]
from fastapi import APIRouter, HTTPException
# pyrefly: ignore [missing-import]
from fastapi.responses import StreamingResponse
# pyrefly: ignore [missing-import]
from pydantic import BaseModel
import qrcode
from io import BytesIO
import json
import base64
from datetime import datetime, timezone

router = APIRouter()


class QRAbsensiRequest(BaseModel):
    jadwal_id: int
    tanggal: str
    kelas: str
    mata_pelajaran: str
    expired_minutes: int = 30


@router.post("/absensi/generate")
async def generate_qr_absensi(request: QRAbsensiRequest):
    """
    Generate QR Code untuk absensi kelas.
    QR berisi token yang expired setelah N menit.
    """
    try:
        # Buat payload QR
        payload = {
            "jadwal_id": request.jadwal_id,
            "tanggal": request.tanggal,
            "kelas": request.kelas,
            "mata_pelajaran": request.mata_pelajaran,
            "generated_at": datetime.now(timezone.utc).isoformat(),
            "expired_at": request.expired_minutes,
        }
        
        # Encode payload
        token = base64.b64encode(json.dumps(payload).encode()).decode()
        
        # Generate QR Code
        qr = qrcode.QRCode(
            version=1,
            error_correction=qrcode.constants.ERROR_CORRECT_H,
            box_size=10,
            border=4,
        )
        qr.add_data(token)
        qr.make(fit=True)
        
        img = qr.make_image(fill_color="#1a5276", back_color="white")
        
        # Convert to bytes
        buffer = BytesIO()
        img.save(buffer, format="PNG")
        buffer.seek(0)
        
        return {
            "success": True,
            "token": token,
            "qr_image_base64": base64.b64encode(buffer.getvalue()).decode(),
            "expired_minutes": request.expired_minutes,
            "jadwal_id": request.jadwal_id,
        }
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/absensi/image/{jadwal_id}/{tanggal}")
async def get_qr_image(jadwal_id: int, tanggal: str):
    """Dapatkan QR Code image langsung"""
    payload = {
        "jadwal_id": jadwal_id,
        "tanggal": tanggal,
        "generated_at": datetime.now(timezone.utc).isoformat(),
    }
    
    token = base64.b64encode(json.dumps(payload).encode()).decode()
    
    qr = qrcode.QRCode(version=1, error_correction=qrcode.constants.ERROR_CORRECT_H, box_size=10, border=4)
    qr.add_data(token)
    qr.make(fit=True)
    img = qr.make_image(fill_color="#1a5276", back_color="white")
    
    buffer = BytesIO()
    img.save(buffer, format="PNG")
    buffer.seek(0)
    
    return StreamingResponse(buffer, media_type="image/png")
