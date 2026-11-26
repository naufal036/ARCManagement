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

---

# Prosedur Backup dan Restore Dokumen

Backup dijalankan manual oleh operator yang berwenang; aplikasi tidak membuat backup otomatis. Simpan backup di media terpisah dari web root dan lindungi dengan enkripsi serta akses terbatas. Jangan menyimpan kata sandi database di command history atau repository.

## Backup

1. Hentikan sementara upload/perubahan dokumen agar snapshot database dan file privat konsisten.
2. Dari PowerShell di root project, isi nilai host, port, user, dan nama database dari konfigurasi lingkungan tanpa menyalin password ke command. Siapkan folder backup yang aman:

   ```powershell
   $backupDirectory = 'E:\ARCManagement-backups'
   $timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
   New-Item -ItemType Directory -Force -Path $backupDirectory
   ```

3. Ganti placeholder pada perintah berikut dengan konfigurasi database. Opsi `--password` meminta password secara interaktif.

   ```powershell
   mysqldump --single-transaction --routines --triggers --default-character-set=utf8mb4 --host=127.0.0.1 --port=3306 --user=DB_USERNAME --password --result-file="$backupDirectory\database-$timestamp.sql" DB_DATABASE
   if ($LASTEXITCODE -ne 0) { throw 'Backup database gagal.' }
   Compress-Archive -Path 'storage\app\private' -DestinationPath "$backupDirectory\document-files-$timestamp.zip"
   ```

4. Pastikan kedua file backup tidak kosong, simpan keduanya dengan timestamp yang sama, lalu lanjutkan kembali perubahan aplikasi.

`--single-transaction` memberikan snapshot konsisten untuk tabel InnoDB. Jangan gunakan prosedur ini saat tabel terkait memakai engine non-transaksional tanpa menghentikan seluruh penulisan selama backup.

## Restore

1. Pulihkan terlebih dahulu ke database dan folder staging yang terisolasi; jangan menimpa database aktif atau folder file aktif sebagai langkah percobaan.
2. Buat database staging kosong dengan charset/collation yang sama. Dari `cmd.exe`, impor dump setelah mengganti placeholder:

   ```text
   mysql --host=127.0.0.1 --port=3306 --user=DB_USERNAME --password --database=ARCManagement_restore < "E:\ARCManagement-backups\database-TIMESTAMP.sql"
   ```

3. Ekstrak arsip file ke direktori staging, bukan langsung ke storage aktif:

   ```powershell
   Expand-Archive -LiteralPath 'E:\ARCManagement-backups\document-files-TIMESTAMP.zip' -DestinationPath 'E:\ARCManagement-restore-staging'
   ```

4. Verifikasi status migration, jumlah record dokumen/versi, keberadaan file, dan checksum sebelum menyetujui cutover. Untuk cutover ke lingkungan aktif, hentikan penulisan, buat backup terbaru, dan lakukan persetujuan operator terpisah. Pertahankan backup sebelumnya sampai pemulihan berhasil dikonfirmasi.
