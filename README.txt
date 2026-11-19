WEB DATA PRODUK — PERTEMUAN 9

Kebutuhan:
- XAMPP (Apache dan MySQL)
- PHP dengan PDO MySQL
- Database web1_pertemuan09

Cara menjalankan:
1. Ekstrak folder web1_pertemuan09_web ke C:\xampp\htdocs\
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka http://localhost/phpmyadmin
4. Pastikan database web1_pertemuan09 sudah dibuat dengan menjalankan praktikum.sql.
   Jika database sudah ada dan berisi data, jangan jalankan INSERT data awal berulang.
5. Periksa config.php. Pengaturan standar XAMPP biasanya user=root dan password kosong.
6. Buka http://localhost/web1_pertemuan09_web/

Fitur:
- Tabel produk dengan JOIN ke tabel kategori
- Pencarian produk/kategori
- Tombol Tambah untuk memasukkan produk baru
- Tombol Simpan untuk menyimpan perubahan produk
- Tombol Hapus pada setiap baris produk
- Ringkasan jumlah produk per kategori dengan LEFT JOIN
- Total kategori dan produk

Catatan:
- Halaman ini mengelola data tabel produk. Kategori dipilih dari tabel kategori yang sudah ada.
- Jangan hapus kategori yang masih digunakan produk karena foreign key ON DELETE RESTRICT.
