<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\VisualisasiRenstra;
use App\Models\PohonKinerja;
use App\Models\IndikatorPohonKinerja;
use App\Models\Tujuan;

class VisualisasiPohonKinerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bersihkan Data Lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        IndikatorPohonKinerja::truncate();
        PohonKinerja::truncate();
        VisualisasiRenstra::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan ada Tujuan untuk referensi Pohon Kinerja
        $tujuan = Tujuan::first();
        if (!$tujuan) {
            $tujuan = Tujuan::create([
                'sasaran_rpjmd' => 'Meningkatnya Kualitas Kesehatan Masyarakat',
                'tujuan' => 'Meningkatnya Derajat Kesehatan Masyarakat',
            ]);
        }

        // =========================================================================
        // A. DATA DUMMY: VISUALISASI POHON KINERJA (BAGAN POHON CANVAS & CETAK PDF)
        // =========================================================================

        // 1. ROOT: Kepala Dinas
        $rootKadis = VisualisasiRenstra::create([
            'parent_id' => null,
            'jabatan' => 'Kepala Dinas',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Derajat Kesehatan Masyarakat melalui Penguatan Sistem Pelayanan Kesehatan yang Inklusif dan Berkelanjutan',
                    'indikators' => [
                        ['nama' => 'Indeks Pembangunan Kesehatan Masyarakat (IPKM)', 'nilai' => '85.20', 'satuan' => 'Poin'],
                        ['nama' => 'Umur Harapan Hidup (UHH)', 'nilai' => '73.50', 'satuan' => 'Tahun'],
                        ['nama' => 'Prevalensi Stunting pada Balita', 'nilai' => '14.00', 'satuan' => '%'],
                        ['nama' => 'Angka Kematian Ibu (AKI) per 100.000 Kelahiran Hidup', 'nilai' => '75.00', 'satuan' => 'per 100.000 KH'],
                    ],
                ],
                [
                    'kinerja_utama' => 'Meningkatnya Tata Kelola Pemerintahan Perangkat Daerah yang Akuntabel dan Efektif',
                    'indikators' => [
                        ['nama' => 'Nilai Evaluasi SAKIP Perangkat Daerah', 'nilai' => '84.50', 'satuan' => 'Nilai (A)'],
                        ['nama' => 'Indeks Pelayanan Publik (IPP)', 'nilai' => '4.25', 'satuan' => 'Skala 5'],
                    ],
                ],
            ],
        ]);

        // 2. LEVEL 1: Eselon III (Sekretaris & Para Kepala Bidang)

        // 2.1 Sekretaris
        $sekretaris = VisualisasiRenstra::create([
            'parent_id' => $rootKadis->id,
            'jabatan' => 'Seketaris',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Kualitas Akuntabilitas Kinerja, Keuangan, dan Pengelolaan Tata Kelola Perangkat Daerah',
                    'indikators' => [
                        ['nama' => 'Persentase Capaian Kinerja Program dan Anggaran Perangkat Daerah', 'nilai' => '95.00', 'satuan' => '%'],
                        ['nama' => 'Opini Laporan Keuangan SKPD dari BPK', 'nilai' => 'WTP', 'satuan' => 'Opini'],
                        ['nama' => 'Tingkat Kepatuhan Pengelolaan Barang Milik Daerah (BMD)', 'nilai' => '92.00', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 2.2 Kepala Bidang Kesehatan Masyarakat
        $kabidKesmas = VisualisasiRenstra::create([
            'parent_id' => $rootKadis->id,
            'jabatan' => 'Kepala Bidang Kesehatan Masyarakat',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Pemberdayaan dan Derajat Kesehatan Ibu, Anak, Gizi Masyarakat, dan Promosi Kesehatan',
                    'indikators' => [
                        ['nama' => 'Persentase Ibu Hamil Mendapatkan Pelayanan Antenatal Standar (K4)', 'nilai' => '92.00', 'satuan' => '%'],
                        ['nama' => 'Persentase Balita Mendapatkan Imunisasi Dasar Lengkap', 'nilai' => '95.00', 'satuan' => '%'],
                        ['nama' => 'Persentase Puskesmas Mampu Tata Laksana Gizi Buruk', 'nilai' => '100.00', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 2.3 Kepala Bidang Pencegahan dan Pengendalian Penyakit (P2P)
        $kabidP2p = VisualisasiRenstra::create([
            'parent_id' => $rootKadis->id,
            'jabatan' => 'Kepala Bidang Pencegahan dan Pengendalian Penyakit',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Menurunnya Angka Kesakitan dan Kematian Akibat Penyakit Menular dan Penyakit Tidak Menular',
                    'indikators' => [
                        ['nama' => 'Persentase Keberhasilan Pengobatan TB (Treatment Success Rate)', 'nilai' => '90.00', 'satuan' => '%'],
                        ['nama' => 'Persentase Penderita Hipertensi dan Diabetes Mendapat Pelayanan Sesuai Standar', 'nilai' => '85.00', 'satuan' => '%'],
                        ['nama' => 'Cakupan Deteksi Dini Faktor Risiko PTM di Masyarakat', 'nilai' => '80.00', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 2.4 Kepala Bidang Pelayanan Kesehatan (Yankes)
        $kabidYankes = VisualisasiRenstra::create([
            'parent_id' => $rootKadis->id,
            'jabatan' => 'Kepala Bidang Pelayanan Kesehatan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Mutu dan Aksesibilitas Fasilitas Pelayanan Kesehatan Primer dan Rujukan',
                    'indikators' => [
                        ['nama' => 'Persentase Fasilitas Pelayanan Kesehatan (Fasyankes) Terakreditasi Paripurna/Utama', 'nilai' => '90.00', 'satuan' => '%'],
                        ['nama' => 'Indeks Kepuasan Masyarakat terhadap Layanan Rumah Sakit dan Puskesmas', 'nilai' => '88.50', 'satuan' => 'Poin'],
                    ],
                ],
            ],
        ]);

        // 2.5 Kepala Bidang Sumber Daya Kesehatan (SDK)
        $kabidSdk = VisualisasiRenstra::create([
            'parent_id' => $rootKadis->id,
            'jabatan' => 'Kepala Bidang Sumber Daya Kesehatan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Terpenuhinya Ketersediaan Obat, Vaksin, Alat Kesehatan, dan SDM Kesehatan yang Kompeten dan Merata',
                    'indikators' => [
                        ['nama' => 'Persentase Ketersediaan Obat dan Vaksin Esensial di Fasyankes', 'nilai' => '98.00', 'satuan' => '%'],
                        ['nama' => 'Persentase Fasyankes yang Memenuhi Standar Ketenagaan Kesehatan', 'nilai' => '86.00', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 3. LEVEL 2: Eselon IV (Kasubbag & Kasi)

        // 3.1 Turunan Sekretaris:
        VisualisasiRenstra::create([
            'parent_id' => $sekretaris->id,
            'jabatan' => 'Kepala Sub Bagian Perencanaan dan Pelaporan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Tersedianya Dokumen Perencanaan dan Evaluasi Kinerja Dinas Kesehatan yang Berkualitas dan Tepat Waktu',
                    'indikators' => [
                        ['nama' => 'Jumlah Dokumen Perencanaan (Renja, RKA, Renstra) yang Tersusun Sesuai Regulasi', 'nilai' => '5', 'satuan' => 'Dokumen'],
                        ['nama' => 'Ketepatan Waktu Penyampaian Laporan Capaian Kinerja (LKPJ, LPPD, LKjIP)', 'nilai' => '100', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $sekretaris->id,
            'jabatan' => 'Kepala Sub Bagian Keuangan dan Asett',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Terselenggaranya Penatausahaan Keuangan dan Pengelolaan Barang Milik Daerah yang Tertib dan Akuntabel',
                    'indikators' => [
                        ['nama' => 'Tingkat Realisasi Anggaran Keuangan Dinas Kesehatan', 'nilai' => '95.50', 'satuan' => '%'],
                        ['nama' => 'Jumlah Laporan Keuangan dan BMD yang Tersusun Sesuai Standar Akuntansi Pemerintah', 'nilai' => '12', 'satuan' => 'Laporan'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $sekretaris->id,
            'jabatan' => 'Kepala Sub Bagian Umum dan Kepegawaian',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Optimalnya Layanan Tata Usaha, Rumah Tangga, dan Administrasi Kepegawaian Perangkat Daerah',
                    'indikators' => [
                        ['nama' => 'Persentase Kebutuhan Sarpras Operasional Kantor yang Terpenuhi', 'nilai' => '100', 'satuan' => '%'],
                        ['nama' => 'Persentase Layanan Kenaikan Pangkat dan Gaji Berkala ASN Tepat Waktu', 'nilai' => '100', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 3.2 Turunan Bidang Kesehatan Masyarakat:
        VisualisasiRenstra::create([
            'parent_id' => $kabidKesmas->id,
            'jabatan' => 'Kepala Seksi Promosi Kesehatan, Pemberdayaan Masyarakat dan Kesehatan Jiwa',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Penerapan Perilaku Hidup Bersih Sehat (PHBS) dan Skrining Kesehatan Jiwa di Masyarakat',
                    'indikators' => [
                        ['nama' => 'Persentase Desa/Kelurahan Siaga Aktif dan Menerapkan PHBS', 'nilai' => '85', 'satuan' => '%'],
                        ['nama' => 'Cakupan Skrining Kesehatan Jiwa pada Kelompok Rentan', 'nilai' => '80', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $kabidKesmas->id,
            'jabatan' => 'Kepala Seksi Kesehatan Keluarga dan Gizi',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Tercapainya Standar Pelayanan Kesehatan Ibu, Anak, Lansia, dan Penurunan Kasus Balita Stunting',
                    'indikators' => [
                        ['nama' => 'Persentase Balita Kurang Gizi yang Mendapat Pemberian Makanan Tambahan (PMT)', 'nilai' => '90', 'satuan' => '%'],
                        ['nama' => 'Cakupan Persalinan di Fasilitas Pelayanan Kesehatan', 'nilai' => '95', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 3.3 Turunan Bidang P2P:
        VisualisasiRenstra::create([
            'parent_id' => $kabidP2p->id,
            'jabatan' => 'Kepala Seksi Pencegahan dan Pengendalian Penyakit Menular dan Kesehatan Lingkungan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Terkendalinya Kasus Penyakit Menular (DBD, Malaria, TB, HIV) dan Pengawasan Higiene Sanitasi Lingkungan',
                    'indikators' => [
                        ['nama' => 'Persentase Kasus DBD yang Ditangani Tepat Waktu (< 24 Jam)', 'nilai' => '100', 'satuan' => '%'],
                        ['nama' => 'Desa/Kelurahan yang Stop Buang Air Besar Sembarangan (Open Defecation Free)', 'nilai' => '82', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $kabidP2p->id,
            'jabatan' => 'Kepala Seksi Surveilans dan Imunisasi',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Terselenggaranya Sistem Kewaspadaan Dini & Respon (SKDR) Serta Kelengkapan Imunisasi Rutin',
                    'indikators' => [
                        ['nama' => 'Kelengkapan dan Ketepatan Laporan SKDR Mingguan', 'nilai' => '96', 'satuan' => '%'],
                        ['nama' => 'Cakupan Desa Universal Child Immunization (UCI)', 'nilai' => '92', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 3.4 Turunan Bidang Pelayanan Kesehatan:
        VisualisasiRenstra::create([
            'parent_id' => $kabidYankes->id,
            'jabatan' => 'Kepala Seksi Pelayanan Kesehatan Primer',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Tersedianya Layanan Kesehatan Dasar di Puskesmas dan Jaringannya Sesuai Standar Mutu Pelayanan Minimal',
                    'indikators' => [
                        ['nama' => 'Persentase Puskesmas yang Memenuhi Standar Pelayanan Minimal (SPM)', 'nilai' => '100', 'satuan' => '%'],
                        ['nama' => 'Tingkat Kepatuhan Puskesmas terhadap Standar Keselamatan Pasien', 'nilai' => '90', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $kabidYankes->id,
            'jabatan' => 'Kepala Seksi Pelayanan Kesehatan Rujukan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Optimalnya Sistem Rujukan Terintegrasi (SISRUTE) dan Mutu Layanan Rumah Sakit Daerah',
                    'indikators' => [
                        ['nama' => 'Tingkat Keberhasilan Respons Kasus Rujukan Gawat Darurat Melalui SISRUTE', 'nilai' => '95', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // 3.5 Turunan Bidang Sumber Daya Kesehatan:
        VisualisasiRenstra::create([
            'parent_id' => $kabidSdk->id,
            'jabatan' => 'Kepala Seksi Sumber Daya Manusia Kesehatan',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Meningkatnya Pemenuhan dan Peningkatan Kompetensi Teknis Tenaga Medis dan Tenaga Kesehatan',
                    'indikators' => [
                        ['nama' => 'Persentase Tenaga Kesehatan yang Mengikuti Pelatihan Teknis/Fungsional Bersertifikat', 'nilai' => '78', 'satuan' => '%'],
                        ['nama' => 'Persentase Puskesmas yang Memiliki 9 Jenis Tenaga Kesehatan Standar', 'nilai' => '85', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        VisualisasiRenstra::create([
            'parent_id' => $kabidSdk->id,
            'jabatan' => 'Kepala Seksi Kefarmasian',
            'is_locked' => true,
            'content_data' => [
                [
                    'kinerja_utama' => 'Tersedianya Pengelolaan dan Distribusi Sediaan Farmasi dan Bahan Medis Habis Pakai yang Aman dan Terpantau',
                    'indikators' => [
                        ['nama' => 'Tingkat Ketersediaan Obat Esensial di Instalasi Farmasi dan Puskesmas', 'nilai' => '98', 'satuan' => '%'],
                    ],
                ],
            ],
        ]);

        // =========================================================================
        // B. DATA DUMMY: CASCADING RENSTRA (TABEL POHON KINERJA)
        // =========================================================================

        // Root Pohon Kinerja
        $pkRoot = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => null,
            'nama_pohon' => 'Meningkatnya Derajat Kesehatan Masyarakat Kalimantan Selatan',
        ]);

        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkRoot->id, 'nama_indikator' => 'Indeks Pembangunan Kesehatan Masyarakat (IPKM)', 'target' => 85.20, 'satuan' => 'Poin']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkRoot->id, 'nama_indikator' => 'Umur Harapan Hidup (UHH)', 'target' => 73.50, 'satuan' => 'Tahun']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkRoot->id, 'nama_indikator' => 'Angka Kematian Ibu (AKI)', 'target' => 75.00, 'satuan' => 'per 100.000 KH']);

        // Anak 1: Upaya Kesehatan Masyarakat & Pencegahan Penyakit
        $pkAnak1 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkRoot->id,
            'nama_pohon' => 'Meningkatnya Upaya Kesehatan Masyarakat dan Pencegahan Penyakit',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak1->id, 'nama_indikator' => 'Cakupan Imunisasi Dasar Lengkap pada Bayi', 'target' => 95.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak1->id, 'nama_indikator' => 'Persentase Penurunan Prevalensi Stunting Balita', 'target' => 14.00, 'satuan' => '%']);

        // Cucu 1.1: Gizi & Kesehatan Keluarga
        $pkCucu11 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkAnak1->id,
            'nama_pohon' => 'Meningkatnya Kesehatan Ibu, Anak, dan Perbaikan Gizi Masyarakat',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu11->id, 'nama_indikator' => 'Persentase Persalinan di Fasilitas Pelayanan Kesehatan', 'target' => 95.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu11->id, 'nama_indikator' => 'Persentase Balita dengan Status Gizi Baik', 'target' => 92.00, 'satuan' => '%']);

        // Cucu 1.2: Pengendalian Penyakit
        $pkCucu12 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkAnak1->id,
            'nama_pohon' => 'Terkendalinya Penyakit Menular dan Faktor Risiko Penyakit Tidak Menular',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu12->id, 'nama_indikator' => 'Treatment Success Rate Pengobatan TB Paru', 'target' => 90.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu12->id, 'nama_indikator' => 'Cakupan Skrining Faktor Risiko PTM pada Usia Produktif', 'target' => 80.00, 'satuan' => '%']);

        // Anak 2: Akses dan Mutu Pelayanan Kesehatan
        $pkAnak2 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkRoot->id,
            'nama_pohon' => 'Meningkatnya Aksesibilitas dan Mutu Pelayanan Kesehatan Fasyankes Primer dan Rujukan',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak2->id, 'nama_indikator' => 'Persentase Fasyankes Terakreditasi Paripurna/Utama', 'target' => 90.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak2->id, 'nama_indikator' => 'Indeks Kepuasan Masyarakat terhadap Pelayanan Kesehatan', 'target' => 88.50, 'satuan' => 'Poin']);

        // Cucu 2.1: Sarpras Fasyankes
        $pkCucu21 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkAnak2->id,
            'nama_pohon' => 'Penguatan Sarana, Prasarana, dan Kesiapan Pelayanan Kesehatan Primer & Rujukan',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu21->id, 'nama_indikator' => 'Persentase Puskesmas Memenuhi Standar Sarpras Permenkes 43', 'target' => 88.00, 'satuan' => '%']);

        // Cucu 2.2: SDM & Farmasi
        $pkCucu22 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkAnak2->id,
            'nama_pohon' => 'Pemerataan Tenaga Kesehatan dan Ketersediaan Obat Esensial di Seluruh Fasyankes',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu22->id, 'nama_indikator' => 'Persentase Puskesmas dengan 9 Jenis Nakes Standar Lengkap', 'target' => 85.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu22->id, 'nama_indikator' => 'Tingkat Ketersediaan Obat dan Vaksin Esensial', 'target' => 98.00, 'satuan' => '%']);

        // Anak 3: Tata Kelola dan Akuntabilitas
        $pkAnak3 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkRoot->id,
            'nama_pohon' => 'Meningkatnya Tata Kelola Pemerintahan dan Akuntabilitas Kinerja Dinas Kesehatan',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak3->id, 'nama_indikator' => 'Nilai SAKIP Dinas Kesehatan', 'target' => 84.50, 'satuan' => 'Nilai (A)']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkAnak3->id, 'nama_indikator' => 'Persentase Realisasi Fisik dan Keuangan SKPD', 'target' => 95.00, 'satuan' => '%']);

        // Cucu 3.1: Keuangan & Aset
        $pkCucu31 = PohonKinerja::create([
            'tujuan_id' => $tujuan->id,
            'parent_id' => $pkAnak3->id,
            'nama_pohon' => 'Optimalisasi Pengelolaan Keuangan, Barang Milik Daerah, dan Perencanaan Kinerja',
        ]);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu31->id, 'nama_indikator' => 'Opini BPK Wajar Tanpa Pengecualian (WTP)', 'target' => 100.00, 'satuan' => '%']);
        IndikatorPohonKinerja::create(['pohon_kinerja_id' => $pkCucu31->id, 'nama_indikator' => 'Tingkat Kepatuhan Pelaporan Keuangan dan BMD Tepat Waktu', 'target' => 100.00, 'satuan' => '%']);

        $this->command?->info("Dummy Data Visualisasi Pohon Kinerja & Cascading Renstra berhasil dibuat!");
    }
}
