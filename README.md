# 👥 Anggota Kelompok dan Pembagian Tugas

Berikut adalah daftar anggota kelompok beserta fokus bagian yang dikerjakan:

| Nama Anggota | NIM | Bagian / Fokus Tugas |
| :--- | :---: | :--- |
| **Hadziq Naufal Sinaga** | 251402095 | Bagian Autentikasi, Hak Akses & Template Dasar |
| **Aulia Syahfira** | 251402104 | Bagian Manajemen & Arsip Dokumen |
| **Ainuha Suraiya** | 251402122 | Bagian Progres & Pelaporan |
| **Aliyah Briek Salem** | 251402010 | Bagian Kontrak & Penugasan Tim |
| **Muhammad Kevin Ramadhan** | 251402013 | Bagian Master Data & Registrasi Proyek |

---

# 📝 Deskripsi Tugas

### 1. Bagian Autentikasi, Hak Akses & Template Dasar
Mengatur sistem Login/Register, membatasi hak akses (multi-role: Direktur, Admin, Pengawas, Staf), mencatat riwayat aktivitas, dan memasang template layout utama (navbar, sidebar, footer) yang bakal dipakai sama semua anggota lain.

*   **Tabel yang dibuat:** `users`, `roles`, `system_configs`, dan `activity_logs`

---

### 2. Bagian Master Data & Registrasi Proyek
Membuat CRUD (tambah, lihat, edit, hapus) untuk data Klien, kategori Jenis Pekerjaan, dan Data Induk Proyek. Termasuk merancang form input proyek baru dan menampilkan daftarnya.

*   **Tabel yang dibuat:** `klien`, `jenis_pekerjaan`, dan `proyek`

---

### 3. Bagian Kontrak & Penugasan Tim
Membangun fitur untuk mengelola data Kontrak dan Adendum (perubahan kontrak), serta membuat halaman agar Admin bisa menugaskan staf/karyawan ke proyek tertentu beserta tenggat waktu pekerjaannya.

*   **Tabel yang dibuat:** `kontrak`, `adendum_kontrak`, `penugasan_proyek`, dan `tugas_proyek`

---

### 4. Bagian Manajemen & Arsip Dokumen
Fokus di pengelolaan file. Membuat fitur upload, download, dan pencarian dokumen proyek, mengelompokkan kategori dokumen, serta mengelola pencatatan revisi kalau ada file yang diperbarui.

*   **Tabel yang dibuat:** `kategori_dokumen`, `dokumen_proyek`, dan `versi_dokumen`

---

### 5. Bagian Progres & Pelaporan
Membuat tampilan untuk mencatat persentase progres harian/mingguan proyek, membuat tombol setuju/tolak dokumen untuk Direktur/Klien, dan menyatukan data-data tersebut ke dalam halaman cetak laporan akhir.

*   **Tabel yang dibuat:** `progres_proyek`, `review_dokumen`, `item_rab`, dan `laporan_proyek`
