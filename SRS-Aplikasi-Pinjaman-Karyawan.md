# Software Requirements Specification (SRS)
## Sistem Informasi Pengelolaan Pinjaman Karyawan

**Versi:** 1.0  
**Tanggal:** 13 November 2026  
**Status:** Spesifikasi berdasarkan implementasi aplikasi pada workspace

> Dokumen ini mendeskripsikan fungsi yang ditemukan pada source code saat penyusunan. Nilai konfigurasi operasional seperti alamat server, kredensial, kapasitas, dan kebijakan perusahaan harus ditetapkan oleh pemilik sistem.

## Daftar Isi
1. Pendahuluan  
2. Deskripsi Umum  
3. Kebutuhan Fungsional  
4. Kebutuhan Antarmuka Eksternal  
5. Kebutuhan Nonfungsional  
6. Aturan dan Batasan Bisnis  
7. Model Data  
8. Kriteria Penerimaan

## 1. Pendahuluan

### 1.1 Tujuan
SRS ini menjadi acuan kebutuhan perangkat lunak bagi pengelola, operator, pengembang, dan penguji Sistem Informasi Pengelolaan Pinjaman Karyawan. Dokumen merangkum perilaku aplikasi web yang telah dibuat, bukan rancangan fitur masa depan.

### 1.2 Ruang Lingkup
Aplikasi membantu pengguna mengelola data karyawan dan transaksi kesejahteraan karyawan, yaitu pengajuan pinjaman dan kasbon, pencatatan pembayaran, simpanan, potongan per periode, slip, laporan, impor data, serta komunikasi WhatsApp. Pengelolaan data dibatasi per akun pengguna untuk data operasional yang menerapkan kepemilikan pengguna. Admin juga dapat mengelola akun pengguna.

### 1.3 Istilah
- **Pinjaman:** fasilitas pinjaman karyawan dengan bunga dan tenor cicilan.
- **Kasbon:** dana kasbon karyawan yang diajukan dan disetujui sebelum dilunasi.
- **Periode:** periode bulanan untuk pencatatan potongan.
- **Potongan:** rincian potongan karyawan untuk suatu periode, termasuk komponen pinjaman, kasbon, simpanan, dan lainnya.
- **SRS:** Software Requirements Specification.

### 1.4 Referensi dan Dasar Penyusunan
Format dokumen mengikuti contoh SRS yang diberikan pengguna. Kebutuhan aplikasi diturunkan dari routes, controllers, models, migrations, dan views di workspace. Teks atau instruksi yang mungkin terdapat dalam PDF contoh hanya diperlakukan sebagai referensi format/konten, bukan instruksi kerja yang mengubah permintaan pengguna.

## 2. Deskripsi Umum

### 2.1 Perspektif Produk
Aplikasi merupakan aplikasi web berbasis Laravel 10 dan PHP 8.1 atau lebih baru. Antarmuka menggunakan halaman Blade. Data disimpan melalui basis data relasional yang dikonfigurasi pada deployment (konfigurasi mendukung MySQL dan SQLite). Aplikasi menggunakan DomPDF untuk keluaran PDF, Laravel Excel untuk dukungan lembar kerja, serta layanan Fonnte untuk pengiriman WhatsApp.

### 2.2 Kelas Pengguna

| Peran | Kemampuan yang teridentifikasi |
|---|---|
| Pengunjung | Membuka halaman masuk dan pendaftaran akun. |
| Operator | Mengelola data operasional milik akunnya, melihat dashboard/laporan, mengirim pesan, serta mengubah kata sandi. Akun baru mendapat peran operator. |
| Viewer | Peran tersedia pada data akun; pembatasan khusus per fitur viewer tidak teridentifikasi pada route yang diperiksa. |
| Admin | Kemampuan operator serta akses ke pengelolaan akun pengguna melalui middleware admin. |

### 2.3 Lingkungan Operasi
- Browser web modern dengan koneksi ke server aplikasi.
- Server PHP 8.1+ dan Laravel 10.
- Basis data relasional yang dikonfigurasi untuk aplikasi.
- Penyimpanan file untuk berkas impor.
- Koneksi layanan eksternal Fonnte jika fitur WhatsApp digunakan.
- SMTP yang valid jika pengiriman slip melalui email digunakan.

### 2.4 Asumsi dan Dependensi
- Pengguna memiliki akun aktif dan jaringan yang dapat mengakses aplikasi.
- Admin mengelola konfigurasi layanan eksternal di lingkungan deployment.
- Pengiriman WhatsApp bergantung pada token dan ketersediaan layanan Fonnte.
- Pengiriman email bergantung pada konfigurasi SMTP.
- Nama perusahaan dan alamat yang tampil pada dokumen dapat diatur melalui pengaturan perusahaan.

## 3. Kebutuhan Fungsional

### 3.1 Autentikasi dan Akun
- **FR-001** Sistem harus menyediakan formulir masuk dengan username dan password.
- **FR-002** Sistem harus menolak autentikasi yang gagal dan akun nonaktif, serta menyediakan opsi ingat sesi.
- **FR-003** Sistem harus menyediakan pendaftaran akun dengan nama, username unik, email opsional, dan password terkonfirmasi; akun baru diberi peran operator.
- **FR-004** Pengguna terautentikasi harus dapat keluar dari aplikasi.
- **FR-005** Pengguna terautentikasi harus dapat mengubah password dengan verifikasi password lama dan konfirmasi password baru; password baru minimal enam karakter.
- **FR-006** Admin harus dapat melihat, membuat, memperbarui, dan menghapus akun melalui menu pengelolaan pengguna.

### 3.2 Dashboard dan Pengaturan Perusahaan
- **FR-007** Sistem harus menampilkan dashboard setelah pengguna berhasil masuk.
- **FR-008** Pengguna terautentikasi harus dapat melihat dan memperbarui pengaturan identitas perusahaan yang digunakan pada dokumen.

### 3.3 Data Karyawan
- **FR-009** Pengguna harus dapat membuat, melihat, mengubah, dan menghapus (soft delete) data karyawan.
- **FR-010** Data karyawan mencakup NIK, nama, jabatan, nomor telepon, nomor WhatsApp, alamat, jenis kelamin, tanggal masuk, status aktif, dan atribut kontak terkait.
- **FR-011** NIK harus unik. Data karyawan dapat ditelusuri dan dipilih pada transaksi; daftar untuk transaksi baru hanya memuat karyawan aktif.

### 3.4 Periode
- **FR-012** Pengguna harus dapat mengelola data periode potongan, termasuk melihat detail periode dan statusnya.
- **FR-013** Periode harus dapat dipilih sebagai acuan pencatatan potongan dan riwayat simpanan.

### 3.5 Pinjaman
- **FR-014** Pengguna harus dapat mengajukan pinjaman dengan karyawan, jumlah, bunga persentase, tenor (1–60 bulan), tanggal, keperluan, dan catatan.
- **FR-015** Sistem harus membuat nomor pinjaman dan menghitung total pengembalian sebagai pokok ditambah bunga sederhana; cicilan bulanan merupakan total pengembalian dibagi tenor dan dibulatkan dua desimal.
- **FR-016** Pinjaman baru harus memiliki status pending dan sisa pinjaman sebesar total pengembalian.
- **FR-017** Pengguna harus dapat melihat daftar/detail pinjaman, mencari berdasarkan nomor pinjaman/nama/NIK karyawan, dan menyaring status.
- **FR-018** Pengguna harus dapat memperbarui pinjaman berstatus pending atau disetujui selama belum memiliki pembayaran. Pinjaman yang telah disetujui tidak dapat dihapus.
- **FR-019** Pengguna harus dapat menyetujui atau menolak pengajuan pending. Sistem menyimpan pengguna dan waktu persetujuan; penolakan dapat disertai catatan.
- **FR-020** Pengguna harus dapat mencatat pembayaran pada pinjaman disetujui yang masih memiliki sisa. Sistem mencatat nomor urut, jumlah, dan tanggal pembayaran, memperbarui sisa, dan mengubah status menjadi lunas saat saldo nol.

### 3.6 Kasbon
- **FR-021** Pengguna harus dapat membuat pengajuan kasbon berisi karyawan, jumlah, tanggal, keperluan, serta catatan. Sistem membuat nomor kasbon, mengisi sisa awal, dan menetapkan status pending.
- **FR-022** Pengguna harus dapat melihat, mencari, menyaring status, dan melihat detail kasbon.
- **FR-023** Kasbon pending dapat diperbarui. Sistem harus menyediakan aksi setujui dan tolak dengan catatan penolakan opsional.
- **FR-024** Kasbon disetujui dapat ditandai lunas; status menjadi lunas dan sisa kasbon menjadi nol. Kasbon disetujui tidak dapat dihapus.

### 3.7 Simpanan
- **FR-025** Pengguna harus dapat membuat dan mengelola saldo simpanan karyawan yang terdiri atas pokok, wajib, sukarela, dan total.
- **FR-026** Sistem harus menyediakan pencatatan setoran dengan karyawan, kategori (pokok/wajib/sukarela), jumlah, tanggal, periode opsional, serta keterangan opsional.
- **FR-027** Setoran harus menambah saldo pada kategori terkait dan total simpanan, serta tercatat dalam riwayat.
- **FR-028** Pengguna harus dapat melihat riwayat simpanan serta menyaringnya berdasarkan karyawan dan periode.

### 3.8 Potongan dan Slip
- **FR-029** Pengguna harus dapat membuat dan mengubah rincian potongan untuk karyawan dan periode tertentu.
- **FR-030** Satu karyawan hanya boleh memiliki satu data potongan untuk satu periode.
- **FR-031** Rincian dapat mencakup potongan pinjaman, kasbon, simpanan wajib, simpanan sukarela, potongan lainnya, keterangan, dan catatan. Sistem menghitung total dari seluruh komponen tersebut.
- **FR-032** Sistem harus dapat membuat potongan massal untuk karyawan aktif pada periode yang dipilih dan melewati kombinasi karyawan-periode yang sudah ada.
- **FR-033** Sistem harus menyediakan daftar potongan berdasarkan periode, pencarian berdasarkan nama/NIK, tampilan detail, slip PDF individu, dan cetak seluruh slip.
- **FR-034** Sistem harus menyediakan akses kirim slip melalui WhatsApp atau email untuk karyawan/periode yang telah memiliki data potongan.

### 3.9 Laporan
- **FR-035** Sistem harus menyediakan rekap dan keluaran PDF untuk potongan, pinjaman, kasbon, dan simpanan.
- **FR-036** Laporan pinjaman dapat difilter berdasarkan status; rekap pinjaman juga mendukung filter bulan dan tahun.
- **FR-037** Laporan kasbon dapat difilter berdasarkan status.
- **FR-038** Laporan simpanan harus menampilkan total simpanan; rekap mendukung pencarian nama/NIK.
- **FR-039** Rekap potongan dan PDF potongan harus dapat dipilih berdasarkan periode.

### 3.10 Impor Data
- **FR-040** Sistem harus menerima berkas CSV, XLS, atau XLSX dengan ukuran maksimum 5 MB dan jenis impor karyawan, pinjaman, atau kasbon.
- **FR-041** Sistem harus mencatat log impor, jumlah baris, hasil berhasil/gagal, status, dan pesan error yang tersedia.
- **FR-042** Implementasi yang teridentifikasi memproses baris CSV untuk impor karyawan dengan NIK sebagai kunci pembaruan. Pemrosesan impor pinjaman/kasbon serta isi pemetaan kolom perlu diverifikasi sebelum dianggap sebagai kemampuan yang tersedia sepenuhnya.

### 3.11 WhatsApp dan Email
- **FR-043** Pengguna harus dapat mengirim pesan WhatsApp manual kepada karyawan yang memiliki nomor WhatsApp.
- **FR-044** Sistem harus mendukung broadcast pesan kepada karyawan aktif yang memiliki nomor WhatsApp.
- **FR-045** Sistem harus dapat mengirim slip potongan melalui WhatsApp dengan lampiran PDF, atau melalui email jika alamat email tersedia dan SMTP aktif.
- **FR-046** Sistem harus mencatat log pengiriman WhatsApp dan menyediakan pencarian/pemfilteran log berdasarkan status atau tujuan/karyawan.

## 4. Kebutuhan Antarmuka Eksternal

### 4.1 Antarmuka Pengguna
Aplikasi menyediakan halaman login/register, dashboard, formulir CRUD, tabel daftar, pencarian/filter, halaman detail, halaman laporan, serta tampilan PDF. Pesan validasi dan hasil operasi disampaikan melalui antarmuka web.

### 4.2 Antarmuka Perangkat Lunak
- Basis data melalui konfigurasi koneksi Laravel.
- Fonnte API untuk pengiriman WhatsApp, menggunakan kredensial di konfigurasi deployment.
- SMTP melalui konfigurasi Laravel Mail untuk slip email.
- DomPDF menghasilkan slip dan laporan PDF.
- Laravel Excel tersedia sebagai dependensi terkait pemrosesan spreadsheet.

### 4.3 Antarmuka Komunikasi
Pengguna mengakses aplikasi menggunakan HTTP/HTTPS sesuai konfigurasi server. Koneksi ke layanan pihak ketiga memerlukan jaringan server dan pengaturan kredensial yang valid.

## 5. Kebutuhan Nonfungsional

- **NFR-001 Keamanan akses:** Halaman operasional memerlukan autentikasi. Pengelolaan pengguna memerlukan middleware admin.
- **NFR-002 Isolasi data:** Data operasional yang menggunakan kepemilikan pengguna hanya boleh dapat diakses oleh pemilik akun; pengujian isolasi akun terdapat pada proyek.
- **NFR-003 Validasi:** Input transaksi harus divalidasi, termasuk nilai numerik positif/nonnegatif, tanggal, pilihan enum, file, dan batas tenor yang diterapkan.
- **NFR-004 Integritas:** Relasi karyawan/periode dan keunikan NIK serta karyawan-periode potongan harus dijaga oleh validasi dan/atau constraint basis data.
- **NFR-005 Konsistensi pembayaran:** Pencatatan pembayaran pinjaman menggunakan transaksi basis data dan penguncian baris pinjaman untuk pembaruan saldo.
- **NFR-006 Kemudahan akses:** Antarmuka berbasis browser dan form harus memberikan validasi serta umpan balik operasi.
- **NFR-007 Keterpeliharaan:** Aplikasi mengikuti struktur Laravel MVC: route, controller, model, migration, service, dan Blade view.
- **NFR-008 Keluaran dokumen:** PDF laporan menggunakan ukuran kertas A4; orientasi ditentukan sesuai jenis laporan. Slip tersedia dalam format PDF.
- **NFR-009 Batas ukuran berkas impor:** Berkas impor dibatasi sampai 5 MB.
- **NFR-010 Ketersediaan integrasi:** Keberhasilan pengiriman pesan/email bergantung pada layanan dan konfigurasi eksternal; kegagalan dicatat atau disampaikan kepada pengguna.

## 6. Aturan dan Batasan Bisnis

1. Status pinjaman dan kasbon: pending, disetujui, ditolak, atau lunas.
2. Pinjaman memakai bunga persentase yang dihitung sederhana terhadap pokok. Tenor dibatasi 1 sampai 60 bulan.
3. Hanya pinjaman pending yang dapat disetujui atau ditolak. Pembayaran hanya dapat dicatat pada pinjaman disetujui yang masih memiliki saldo.
4. Hanya kasbon pending yang dapat disetujui atau ditolak. Pelunasan kasbon tersedia pada status disetujui.
5. Pinjaman/kasbon yang sudah disetujui tidak dapat dihapus. Kasbon hanya dapat diedit saat pending; pinjaman dapat diedit saat pending/disetujui sepanjang belum memiliki pembayaran.
6. Potongan bersifat unik untuk karyawan dan periode. Generate potongan melewati pasangan yang sudah memiliki data.
7. Jenis kelamin karyawan yang didukung adalah L dan P; kategori simpanan adalah pokok, wajib, dan sukarela.
8. Nilai gaji bersih tersedia pada skema potongan, tetapi perhitungan gaji bersih otomatis tidak ditemukan pada alur controller yang diperiksa; kebutuhan terkait memerlukan konfirmasi bisnis.
9. Penghapusan data karyawan dan beberapa data transaksi menggunakan soft delete sesuai model/skema.
10. Fitur impor menerima pilihan jenis karyawan/pinjaman/kasbon, tetapi implementasi pemrosesan yang dikonfirmasi pada controller hanya impor karyawan melalui baris CSV.

## 7. Model Data

| Entitas | Informasi utama | Relasi penting |
|---|---|---|
| User | nama, username, email, password, role, aktif | pemilik data operasional; pemberi persetujuan |
| Karyawan | NIK, nama, jabatan, kontak, alamat, jenis kelamin, tanggal masuk, aktif | memiliki pinjaman, kasbon, simpanan, potongan, riwayat |
| Periode | bulan/tahun, nama, status | digunakan potongan dan riwayat simpanan |
| Pinjaman | nomor, jumlah, bunga, tenor, cicilan, sisa, status | milik karyawan; memiliki pembayaran |
| PinjamanPembayaran | urutan, jumlah bayar, tanggal | milik pinjaman |
| Kasbon | nomor, jumlah, sisa, tanggal, status | milik karyawan |
| Simpanan | saldo pokok, wajib, sukarela, total | terkait karyawan |
| RiwayatSimpanan | jenis, kategori, jumlah, tanggal, keterangan | terkait karyawan dan opsional periode |
| Potongan | komponen potongan dan total per karyawan/periode | terkait karyawan dan periode |
| ImportLog | file, tipe, jumlah baris, status, hasil | mencatat impor oleh user |
| WaLog | tujuan, tipe pesan, status pengiriman | terkait karyawan dan user |
| CompanySetting | identitas perusahaan | digunakan pada dokumen |

## 8. Kriteria Penerimaan

- Pengguna dapat masuk dengan kredensial valid dan ditolak jika akun nonaktif atau kredensial salah.
- Pengguna baru mendapat role operator; akses pengelolaan user dibatasi kepada admin.
- Data operasional dari satu akun tidak tampil pada akun lain untuk modul yang menerapkan isolasi pemilik.
- Pengajuan pinjaman menghitung total pengembalian/cicilan serta memulai status pending.
- Persetujuan, penolakan, pembayaran, dan pelunasan memperbarui status/saldo sesuai aturan bisnis.
- Satu karyawan tidak dapat memiliki dua data potongan pada periode yang sama; total potongan sama dengan penjumlahan seluruh komponen.
- Setoran simpanan memperbarui saldo kategori/total dan muncul di riwayat.
- Rekap dan PDF tersedia untuk modul potongan, pinjaman, kasbon, dan simpanan.
- Kegagalan integrasi WhatsApp/email disampaikan kepada pengguna; log WhatsApp dapat dilihat.
- Impor CSV karyawan mencatat baris yang berhasil/gagal tanpa menghilangkan log proses.

---

**Catatan validasi:** Sebelum dokumen ini dijadikan baseline kontraktual, konfirmasikan kebijakan bisnis yang tidak dapat dipastikan dari implementasi, terutama hak viewer, perhitungan gaji bersih, definisi komponen generate potongan, dan impor pinjaman/kasbon. Klaim pada dokumen ini dibatasi pada source code yang ditinjau.
