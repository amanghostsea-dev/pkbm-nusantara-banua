"""
Analitik & Dashboard Service — PKBM
Menyediakan agregasi data analitik, statistik nilai, dan tren kelulusan
"""

from typing import Optional, Dict, Any, List
from datetime import datetime, timezone


class AnalitikService:
    """Service penyedia data agregasi analitik untuk dashboard PKBM"""

    async def get_ringkasan(self, tahun_ajaran: Optional[str] = None) -> Dict[str, Any]:
        """Data ringkasan untuk dashboard utama"""
        return {
            "total_siswa_aktif": 348,
            "distribusi_program": {
                "paket_a": 56,
                "paket_b": 112,
                "paket_c": 180,
            },
            "rata_rata_kehadiran": 88.5,
            "rata_rata_nilai": 81.4,
            "total_lulus_tahun_ini": 94,
            "persentase_kelulusan": 96.2,
            "tutor_aktif": 24,
            "kelas_aktif": 12,
            "updated_at": datetime.now(timezone.utc).isoformat(),
        }

    async def get_distribusi_siswa(self, tahun_ajaran: Optional[str] = None) -> Dict[str, Any]:
        """Distribusi siswa per program, gender, dan kelompok usia"""
        return {
            "per_program": [
                {"name": "Paket A (SD)", "value": 56, "color": "#3498db"},
                {"name": "Paket B (SMP)", "value": 112, "color": "#2ecc71"},
                {"name": "Paket C (SMA)", "value": 180, "color": "#f39c12"},
            ],
            "per_gender": {
                "laki_laki": 195,
                "perempuan": 153,
            },
            "per_kelompok_usia": [
                {"range": "< 18 tahun", "jumlah": 85},
                {"range": "18 - 25 tahun", "jumlah": 142},
                {"range": "26 - 35 tahun", "jumlah": 78},
                {"range": "> 35 tahun", "jumlah": 43},
            ],
        }

    async def get_statistik_nilai(
        self,
        program: Optional[str] = None,
        semester: Optional[int] = None,
        tahun_ajaran: Optional[str] = None,
    ) -> Dict[str, Any]:
        """Statistik nilai: rata-rata, min, max, distribusi mapel"""
        return {
            "rata_rata_umum": 81.4,
            "nilai_tertinggi": 98.0,
            "nilai_terendah": 62.0,
            "mapel_stats": [
                {"mapel": "Matematika", "rata_rata": 77.5, "kkm": 70},
                {"mapel": "Bahasa Indonesia", "rata_rata": 84.2, "kkm": 75},
                {"mapel": "Bahasa Inggris", "rata_rata": 79.1, "kkm": 70},
                {"mapel": "IPA / Sains", "rata_rata": 80.8, "kkm": 70},
                {"mapel": "IPS / Sosial", "rata_rata": 83.6, "kkm": 70},
                {"mapel": "Pemberdayaan / Keterampilan", "rata_rata": 86.4, "kkm": 75},
            ]
        }

    async def get_absensi_bulanan(self, tahun: int, program: Optional[str] = None) -> List[Dict[str, Any]]:
        """Rekap absensi bulanan dalam setahun"""
        bulan_list = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"]
        kehadiran_sample = [86, 88, 89, 87, 85, 90, 92, 91, 88, 89, 87, 88]
        return [
            {"bulan": b, "persentase_hadir": k, "izin": 100 - k - 3, "alpha": 3}
            for b, k in zip(bulan_list, kehadiran_sample)
        ]

    async def get_historis_kelulusan(self, tahun_mulai: Optional[int] = None) -> List[Dict[str, Any]]:
        """Tren kelulusan 5 tahun terakhir"""
        return [
            {"tahun": "2020", "paket_a": 92.0, "paket_b": 94.5, "paket_c": 91.0},
            {"tahun": "2021", "paket_a": 93.5, "paket_b": 95.0, "paket_c": 93.0},
            {"tahun": "2022", "paket_a": 95.0, "paket_b": 96.2, "paket_c": 94.5},
            {"tahun": "2023", "paket_a": 96.0, "paket_b": 97.0, "paket_c": 95.8},
            {"tahun": "2024", "paket_a": 97.5, "paket_b": 98.1, "paket_c": 96.9},
        ]

    async def get_rekap_pembayaran(self, bulan: Optional[int] = None, tahun: Optional[int] = None) -> Dict[str, Any]:
        """Rekap status iuran/pembayaran siswa"""
        return {
            "lunas": 298,
            "menunggak": 50,
            "persentase_lancar": 85.6,
            "total_terkumpul": 149000000,
        }

    async def get_performa_kelas(self, kelas_id: int, semester: Optional[int] = None) -> Dict[str, Any]:
        return {
            "kelas_id": kelas_id,
            "rata_rata_kelas": 82.3,
            "tingkat_kehadiran": 89.2,
            "jumlah_siswa": 28,
        }

    async def get_tren_nilai(self, program: Optional[str] = None, tahun: Optional[int] = None) -> List[Dict[str, Any]]:
        return [
            {"semester": 1, "rata_rata": 79.2},
            {"semester": 2, "rata_rata": 80.5},
            {"semester": 3, "rata_rata": 81.8},
            {"semester": 4, "rata_rata": 82.4},
            {"semester": 5, "rata_rata": 83.1},
            {"semester": 6, "rata_rata": 84.0},
        ]
