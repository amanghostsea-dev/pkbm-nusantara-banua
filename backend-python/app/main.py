"""
PKBM FastAPI Application - Main Entry Point
Sistem Informasi PKBM Paket A, B & C
"""

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.middleware.gzip import GZipMiddleware
from fastapi.responses import JSONResponse
from contextlib import asynccontextmanager
from loguru import logger

from app.core.config import settings
from app.core.database import engine, Base
from app.api.v1.router import api_router


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application startup and shutdown events"""
    logger.info("🚀 PKBM FastAPI Service starting up...")
    logger.info(f"📊 Environment: {settings.ENVIRONMENT}")
    logger.info(f"🗄️  Database: {settings.DATABASE_URL}")
    
    # Create tables if database is reachable
    try:
        async with engine.begin() as conn:
            await conn.run_sync(Base.metadata.create_all)
        logger.info("✅ Database tables verified and ready!")
    except Exception as e:
        logger.warning(f"⚠️ Database not ready yet ({e}). Running in standalone mode.")
    
    logger.info("✅ PKBM FastAPI Service ready!")
    yield
    logger.info("🛑 PKBM FastAPI Service shutting down...")


app = FastAPI(
    title="PKBM API Service",
    description="""
    ## Sistem Informasi PKBM — Python Microservice
    
    Layanan Python untuk:
    - 📄 **Generate Raport PDF** otomatis
    - 🎓 **Generate Sertifikat** kelulusan
    - 📊 **Dashboard Analitik** & statistik
    - 🤖 **Prediksi Kelulusan** siswa (Machine Learning)
    - 📈 **Laporan Dinas** format resmi (Excel/PDF)
    - 📦 **Export Data** bulk
    """,
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
    lifespan=lifespan,
)

# ================================
# Middleware
# ================================
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.add_middleware(GZipMiddleware, minimum_size=1000)

# ================================
# Routers
# ================================
app.include_router(api_router, prefix="/api/v1")


# ================================
# Root Endpoint
# ================================
@app.get("/", tags=["Health"])
async def root():
    return {
        "service": "PKBM Python Microservice",
        "version": "1.0.0",
        "status": "running",
        "docs": "/docs",
    }


@app.get("/health", tags=["Health"])
async def health_check():
    return {
        "status": "healthy",
        "service": "pkbm-python-api",
        "version": "1.0.0",
    }
