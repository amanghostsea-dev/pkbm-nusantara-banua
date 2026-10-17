"""
Application Configuration — PKBM FastAPI
"""

from pydantic_settings import BaseSettings
from typing import List
from pathlib import Path
import os

BASE_DIR = Path(__file__).resolve().parent.parent.parent


class Settings(BaseSettings):
    # ================================
    # App Config
    # ================================
    APP_NAME: str = "PKBM Python Microservice"
    VERSION: str = "1.0.0"
    ENVIRONMENT: str = "development"
    DEBUG: bool = True
    
    # ================================
    # Database
    # ================================
    DATABASE_URL: str = "mysql+pymysql://pkbm_user:pkbm_secret@localhost:3306/pkbm_db"
    DATABASE_POOL_SIZE: int = 10
    DATABASE_MAX_OVERFLOW: int = 20
    
    # ================================
    # Redis
    # ================================
    REDIS_URL: str = "redis://localhost:6379"
    CACHE_TTL: int = 3600  # 1 hour
    
    # ================================
    # Security
    # ================================
    SECRET_KEY: str = "pkbm-super-secret-key-change-in-production"
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 1440  # 24 hours
    
    # ================================
    # Laravel API (internal communication)
    # ================================
    LARAVEL_API_URL: str = "http://laravel:9000"
    LARAVEL_API_KEY: str = "internal-api-key-change-me"
    
    # ================================
    # CORS
    # ================================
    ALLOWED_ORIGINS: List[str] = [
        "http://localhost",
        "http://localhost:80",
        "http://localhost:3000",
        "http://127.0.0.1",
    ]
    
    # ================================
    # Storage (Cross-platform compatible)
    # ================================
    STORAGE_PATH: str = os.getenv("STORAGE_PATH", str(BASE_DIR / "storage"))
    RAPORT_PATH: str = os.getenv("RAPORT_PATH", str(BASE_DIR / "storage" / "raport"))
    SERTIFIKAT_PATH: str = os.getenv("SERTIFIKAT_PATH", str(BASE_DIR / "storage" / "sertifikat"))
    TEMP_PATH: str = os.getenv("TEMP_PATH", str(BASE_DIR / "storage" / "temp"))
    
    # ================================
    # ML Model Config
    # ================================
    ML_MODEL_PATH: str = os.getenv("ML_MODEL_PATH", str(BASE_DIR / "app" / "ml" / "models"))
    ML_RETRAIN_THRESHOLD: int = 100  # Retrain after N new samples
    
    # ================================
    # Pagination
    # ================================
    DEFAULT_PAGE_SIZE: int = 20
    MAX_PAGE_SIZE: int = 100

    class Config:
        env_file = ".env"
        case_sensitive = True


settings = Settings()
