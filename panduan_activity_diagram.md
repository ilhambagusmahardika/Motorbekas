# Panduan Pembuatan Activity Diagram (UML Swimlane Lengkap 10 Menu)

Berikut ini adalah *blueprint* untuk menggambar Activity Diagram di Visio/Draw.io. Buatlah **2 kolom vertical (Swimlane)**: Kolom Kiri untuk **Aktor (Pengguna)**, dan Kolom Kanan untuk **Sistem Jual Motor**.

---

## 1. Activity Diagram: Login
_Aktor: Semua Pengguna (Admin / Staff Admin)_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses aplikasi / halaman login | |
| | 2. Menampilkan Antarmuka Halaman Login |
| 3. Memasukkan Username & Password, lalu tekan Masuk | |
| | 4. Melakukan query verifikasi ke DB `users` |
| | 5. **[Decision]** Apakah Kredensial Valid? |
| | *(Jika Tidak)* 6a. Menampilkan Error -> Kembali ke Aktor no. 3 |
| | *(Jika Ya)* 6b. Menyimpan Data Sesi `$_SESSION` |
| | 7. Mengalihkan (Redirect) ke Beranda |
| **(Selesai)** | |

---

## 2. Activity Diagram: Menu Beranda
_Aktor: Admin / Staff Admin_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses Menu Beranda | |
| | 2. Melakukan agregasi data (Total Terjual, Profit Bulan Ini) |
| | 3. Autentikasi batas waktu pajak semua motor Aktif |
| | 4. Menampilkan Dashboard dan Notifikasi Jatuh Tempo Pajak |
| 5. **[Decision]** Ada Aksi? | |
| *(Jika Tidak)* 5a. **(Selesai)** | |
| *(Jika Ada Pajak)* 5b. Klik tombol "Bayar" | |
| | 6. Menampilkan Pop-up / Form Pembayaran Pajak |
| 7. Input Masa Pajak Baru & Total Biaya Keluar | |
| | 8. Simpan biaya ke DB Perbaikan (Masuk Hitungan Modal) |
| | 9. Refresh Halaman |
| **(Selesai)** | |

---

## 3. Activity Diagram: Pembelian Motor
_Aktor: Admin / Staff Admin_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses Menu Pembelian Motor | |
| | 2. Menampilkan Form Data Pembelian |
| 3. Input Data (Merek, Harga, CC, Penjual) | |
| 4. Input Detail Plat Nomor | |
| | 5. API mengecek keberadaan kode wilayah Plat di Master Plat |
| | 6. **[Decision]** Wilayah Plat Ditemukan? |
| | *(Jika Tidak)* 7a. Muncul Alert Error (Tertolak) -> Kembali ke Aktor no. 3 |
| 8. Menekan tombol "Simpan Data Motor"| *(Bisa lanjut jika plat Valid)* |
| | 9. Mengunggah/Upload Foto Motor ke Direktori Web |
| | 10. Menyimpan Seluruh Data ke DB `motor` (Status: "Dibeli") |
| **(Selesai)** | |

---

## 4. Activity Diagram: Inventori Barang
_Aktor: Admin / Staff Admin_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses Menu Inventori Barang | |
| | 2. Filter DB `motor` (Hanya status "Dibeli") |
| | 3. Menampilkan daftar motor yang belum siap jual |
| 4. Klik "Detail" motor yang butuh servis | |
| | 5. Tampilkan Rincian Kalkulasi Harga Beli vs Harga Perbaikan |
| 6. Input Biaya Servis / Ganti Part | |
| | 7. Simpan tambahan biaya ke DB `perbaikan` |
| 8. Mengeklik Tombol "Siap Jual" | |
| | 9. Mengubah Status Motor menjadi "Siap Jual" |
| **(Selesai)** | |

---

## 5. Activity Diagram: Stok Unit
_Aktor: Admin / Staff Admin_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Buka halaman Stok Unit Siap Jual | |
| | 2. Query data motor (Hanya status "Siap Jual") |
| | 3. Kalkulasi harga jual minimal otomatis (+Margin) |
| | 4. Tampilkan Tabel Daftar Motor Tersedia |
| 5. Memeriksa/Mencari Stok Motor Berdasarkan Merek | |
| | 6. Memfilter teks tabel Real-Time |
| **(Selesai)** | |

---

## 6. Activity Diagram: Transaksi (Penjualan Motor)
_Aktor: Admin / Staff Admin_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Masuk halaman Penjualan | |
| | 2. Tampilkan Form & Dropdown pilihan motor "Siap Jual" |
| 3. Menginput Nama Tunai Transaksi & Harga Deal | |
| | 4. **[Decision]** Harga Beli < Harga Total Modal ?|
| | *(Jika Ya / Rugi)* 5a. Munculkan Peringatan Validasi Merah |
| 6. Menekan Tombol "Simpan Transaksi" | *(Jika Tidak/Profit - Tombol aktif)* |
| | 7. Simpan Riwayat ke DB `penjualan` |
| | 8. Mengubah Keterangan DB `motor` menjadi status "Terjual" |
| **(Selesai)** | |

---

## 7. Activity Diagram: Laporan Keuangan
_Aktor: Admin Khusus (Role Admin)_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses Menu Laporan Keuangan | |
| | 2. Cek Hak Akses (Jika Staff -> Muncul 'Akses Ditolak') |
| | 3. Query Gabungan Tabel Motor (Beli) + Perbaikan (Servis) + Penjualan |
| | 4. Kalkulasi Kronologi Tanggal (Format Arus Kas / Buku Kas) |
| | 5. Menampilkan Halaman Laporan Buku Kas dan Sisa Aset |
| 6. Klik "Cetak Laporan / Print" | |
| | 7. Trigger Browser Print Dialog UI |
| **(Selesai)** | |

---

## 8. Activity Diagram: Master Plat
_Aktor: Admin / Hak Penuh_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Mengakses Menu Master Plat | |
| | 2. Query dan tampilkan seluruh index huruf wilayah Plat |
| 3. Input Inisial Plat, Kota, dan Samsat | |
| 4. Klik Simpan Plat | |
| | 5. Menyimpan entitas ke DB `master_plat` |
| | 6. Refresh tabel secara instan (Real-time update) |
| **(Selesai)** | |

---

## 9. Activity Diagram: Master Motor
_Aktor: Admin / Hak Penuh_

| Aktor | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Masuk ke halaman Master Merek & Model | |
| | 2. Hubungkan Tabel relasi Merek (Master) dengan Tipe (Model) |
| | 3. Tampilkan Dua Tabel Bersamaan |
| 4. Mengklik Header Tombol "Saring Merek X" | |
| | 5. Melenyapkan (Hide) model yang bukan turunan dari "Merek X" |
| 6. Klik Ikon Edit Pencils | |
| | 7. Menampilkan Modal Popup Overlay Form Edit |
| 8. Mengubah teks "Tipe Y" Menjadi "Tipe Z" & Simpan| |
| | 9. Mengupdate DB `master_model` / `master_merek` bersangkutan |
| **(Selesai)** | |

---

## 10. Activity Diagram: Manajemen Karyawan
_Aktor HANYA: Admin Utama_

| Aktor HANYA: Admin | Sistem Aplikasi |
| :--- | :--- |
| **(Mulai)**<br> 1. Memilih Menu Manajemen Karyawan | |
| | 2. Mengecek Sesi Hak Akses |
| | 3. **[Decision]** Sesi Saat Ini = Admin? |
| | *(Jika Bukan)* 4a. Muncul Halaman Ditolak -> Selesai. |
| | *(Jika Ya)* 4b. Query Data Seluruh Staff & Username Terdaftar |
| | 5. Menampilkan Halaman Daftar Karyawan Hak Penuh |
| 6. Menekan tombol Registrasi Tambah (Input Form) | |
| | 7. Proses Input -> Enkripsi / Hash Salting Password Baru |
| | 8. Simpan ke database otentikasi `users` |
| **(Selesai)** | |
