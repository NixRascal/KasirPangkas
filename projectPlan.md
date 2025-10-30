# 1. Modul Inti

* POS kasir. Order multi item per pelanggan. Penyesuaian harga per item. Pilih karyawan per item. Multi metode pembayaran.
* Master data. Jasa, kategori, karyawan, pelanggan, kursi, aturan komisi, promo.
* Shift dan kas. Buka tutup kas, kas kecil, audit selisih.
* Komisi. Hitung otomatis per item berdasarkan karyawan dan jasa.
* Laporan dan dashboard. Operasional untuk Admin. Ringkasan bisnis untuk Stakeholder.
* Akses pengguna. Kasir. Admin. Stakeholder baca saja.

# 2. Skema Basis Data

## 2.1. Pengguna dan Akses

**users**

* id. uuid.
* name. string.
* email. string unique nullable.
* phone. string unique nullable.
* password. string nullable untuk akun kasir berbasis PIN.
* pin. string nullable.
* role. enum kasir. admin. stakeholder.
* is_active. boolean.
* last_login_at. datetime nullable.
* timestamps. soft deletes.

**password_resets** standar Laravel jika perlu.

## 2.2. Master Data Operasional

**service_categories**

* id. uuid.
* name. string.
* description. text nullable.
* order. smallInteger.
* timestamps.

**services**

* id. uuid.
* service_category_id. fk.
* name. string.
* code. string unique.
* base_price. decimal(12,2).
* est_duration_min. smallInteger.
* commission_type. enum percent. flat.
* commission_value. decimal(12,2).
* is_active. boolean.
* timestamps. soft deletes.

**employees**

* id. uuid.
* name. string.
* code. string unique.
* level. enum junior. senior. master.
* phone. string nullable.
* is_active. boolean.
* hire_date. date nullable.
* timestamps.

**customers**

* id. uuid.
* name. string.
* phone. string nullable unique.
* notes. text nullable.
* type. enum reguler. member. vip.
* timestamps. soft deletes.

**chairs**

* id. uuid.
* name. string.
* location. string nullable.
* is_active. boolean.
* timestamps.

## 2.3. POS dan Pembayaran

**orders**

* id. uuid.
* order_no. string unique.
* customer_id. fk nullable. jika tidak ingin simpan data pelanggan bisa dikosongkan.
* status. enum draft. paid. void.
* subtotal. decimal(12,2).
* discount_total. decimal(12,2).
* surcharge_total. decimal(12,2).
* tax_total. decimal(12,2).
* grand_total. decimal(12,2).
* paid_total. decimal(12,2).
* change_due. decimal(12,2).
* notes. text nullable.
* cashier_id. fk ke users.
* shift_id. fk ke shifts.
* cash_session_id. fk ke cash_sessions.
* paid_at. datetime nullable.
* timestamps.

**order_items**

* id. uuid.
* order_id. fk.
* service_id. fk.
* employee_id. fk siapa yang mengerjakan.
* chair_id. fk nullable.
* person_label. string. contoh Anak 1. Ayah. Ibu.
* qty. integer. default 1.
* unit_price. decimal(12,2) harga dasar ditarik dari services.base_price.
* manual_price. decimal(12,2) nullable jika disesuaikan.
* manual_reason. string nullable. alasan wajib jika manual_price terisi.
* manual_by. fk users nullable.
* discount_amount. decimal(12,2).
* line_total. decimal(12,2) hasil akhir per item.
* started_at. datetime nullable.
* finished_at. datetime nullable.
* timestamps.

**payments**

* id. uuid.
* order_id. fk.
* method. enum cash. qris. debit. ewallet. transfer.
* amount. decimal(12,2).
* reference_no. string nullable. contoh nomor trace QRIS.
* paid_by. string nullable nama di kartu jika perlu.
* received_by. fk users.
* paid_at. datetime.
* timestamps.

**promotions** opsional kalau ingin.

* id. uuid.
* name. string.
* type. enum percent. flat.
* value. decimal(12,2).
* start_date. date.
* end_date. date.
* is_active. boolean.
* conditions_json. json. contoh min layanan atau jam tertentu.
* timestamps.

**order_item_promotions** opsional.

* id. uuid.
* order_item_id. fk.
* promotion_id. fk.
* discount_amount. decimal(12,2).

## 2.4. Shift dan Kas

**shifts**

* id. uuid.
* name. string. contoh Pagi. Sore.
* start_time. time.
* end_time. time.
* is_active. boolean.
* timestamps.

**cash_sessions**

* id. uuid.
* shift_id. fk.
* opened_by. fk users.
* closed_by. fk users nullable.
* opened_at. datetime.
* closed_at. datetime nullable.
* opening_float. decimal(12,2).
* closing_cash_counted. decimal(12,2) nullable.
* cash_expected. decimal(12,2) nullable.
* variance. decimal(12,2) nullable.
* notes. text nullable.
* timestamps.

**cash_ledgers**

* id. uuid.
* cash_session_id. fk.
* order_id. fk nullable.
* type. enum cash_in. cash_out.
* reason. string.
* amount. decimal(12,2).
* created_by. fk users.
* created_at. datetime.

## 2.5. Komisi

**commission_rules**

* id. uuid.
* name. string.
* scope. enum per_service. per_employee_level. global.
* service_id. fk nullable.
* employee_level. enum nullable.
* type. enum percent. flat.
* value. decimal(12,2).
* start_date. date nullable.
* end_date. date nullable.
* is_active. boolean.
* timestamps.

**commissions**

* id. uuid.
* order_item_id. fk.
* employee_id. fk.
* rule_id. fk nullable.
* base_amount. decimal(12,2).
* commission_amount. decimal(12,2).
* settled. boolean.
* settled_at. datetime nullable.
* timestamps.

## 2.6. Audit dan Log

**activity_logs**

* id. uuid.
* user_id. fk nullable.
* action. string. contoh price_adjustment. order_void. login.
* subject_type. morph.
* subject_id. morph.
* meta. json.
* created_at. datetime.

Indeks penting. unique pada order_no dan services.code. index pada foreign key. index pada orders.paid_at dan status untuk laporan.

# 3. Eloquent Model dan Relasi

**User**

* hasMany Orders sebagai cashier.
* hasMany Payments sebagai received_by.
* hasMany ActivityLogs.

**Service**

* belongsTo ServiceCategory.
* hasMany OrderItems.

**Employee**

* hasMany OrderItems.
* hasMany Commissions.

**Customer**

* hasMany Orders.

**Chair**

* hasMany OrderItems.

**Order**

* belongsTo Customer.
* belongsTo User cashier.
* belongsTo Shift.
* belongsTo CashSession.
* hasMany OrderItems.
* hasMany Payments.

**OrderItem**

* belongsTo Order.
* belongsTo Service.
* belongsTo Employee.
* belongsTo Chair.
* hasMany OrderItemPromotions.
* hasOne Commission.

**Payment**

* belongsTo Order.
* belongsTo User received_by.

**CommissionRule**

* hasMany Commissions.

**Commission**

* belongsTo OrderItem.
* belongsTo Employee.
* belongsTo CommissionRule nullable.

**CashSession**

* belongsTo Shift.
* hasMany Orders.
* hasMany CashLedgers.

**CashLedger**

* belongsTo CashSession.
* belongsTo Order nullable.

**ActivityLog**

* morphTo subject.

# 4. Controller dan Rute

## 4.1. Rute Web

* Auth standar Laravel Breeze atau Fortify.
* Middleware peran. role.kasir. role.admin. role.stakeholder.

**Kasir**

* GET  pos. index. layar kasir.
* POST pos/orders. store order draft.
* POST pos/orders. add item.
* PATCH pos/orders. update item. ganti karyawan. ubah harga. set diskon.
* POST pos/orders.checkout. proses pembayaran. multi metode.
* POST pos/orders.payment. tambah pembayaran tambahan.
* POST pos/orders.print. cetak struk.
* POST pos/orders.void. batalkan order dengan alasan dan otorisasi admin jika sudah paid.
* POST pos/cash-sessions.open. buka kas.
* POST pos/cash-sessions.close. tutup kas.
* POST pos/cash-ledger. in out kas kecil.

**Admin**

* Resource services. index create store edit update destroy.
* Resource service_categories.
* Resource employees.
* Resource customers.
* Resource shifts.
* Resource commission_rules.
* GET reports. sales daily. by service. by employee. cash summary. discount usage.
* POST orders.refund. parsial atau penuh jika diaktifkan kebijakan.
* GET logs. activity.

**Stakeholder**

* GET dashboard. kpi ringkas.
* GET reports snapshot. unduh pdf atau csv.

## 4.2. Controller Utama

* PosController. index. scan cepat. cart state di session.
* OrderController. store. show. update. destroy. checkout. print. void.
* OrderItemController. store. update. destroy. assignEmployee. overridePrice.
* PaymentController. store. destroy.
* CashSessionController. open. close. summary.
* CashLedgerController. store.
* ServiceController. kategoriController. EmployeeController. CustomerController.
* CommissionRuleController. CommissionController hanya untuk rekap.
* ReportController. SalesReport. EmployeeReport. CashReport.
* DashboardController.

Aksi khusus.

* OrderItemController.overridePrice. validasi batas toleransi. require reason. log activity. jika melewati batas perlu header otorisasi admin.
* OrderController.checkout. validasi cash_session terbuka. hitung ulang total. simpan pembayaran. generate commissions.

# 5. Kebijakan Akses dan Validasi

* Gunakan Gates atau Policies per model.
* Kasir. CRUD order dan pembayaran. Tidak boleh hapus master data. Override harga hanya sampai batas yang ditentukan konfigurasi.
* Admin. Semua kecuali penghapusan permanen. Void setelah paid membutuhkan alasan.
* Stakeholder. Read only. Unduh laporan.

Validasi bisnis.

* manual_price tidak boleh kosong jika manual_reason terisi. Atau sebaliknya.
* override di atas batas perlu admin_id sebagai approver di activity_logs.meta.
* setiap pembayaran harus membuat jejak ke cash_ledgers jika method cash.

# 6. Layanan Domain dan Alur Perhitungan

Buat service class agar controller tetap tipis.

* PricingService.

  * Hitung unit_price default.
  * Terapkan promo. diskon manual. manual_price jika ada. hasilkan line_total.
* CommissionService.

  * Ambil rule terbaik. prioritas per_service. lalu per_employee_level. lalu global.
  * Hitung base_amount. gunakan unit_price setelah diskon.
  * Simpan ke commissions.
* CashService.

  * Buka tutup kas. hitung expected cash dari payments cash dan cash_ledgers.
* OrderWorkflow.

  * Tambah item. ganti karyawan. override harga. checkout. void.
  * Set started_at dan finished_at jika memakai timer layanan.

# 7. Seeder dan Factory

* ServiceCategorySeeder dan ServiceSeeder. Contoh Potong Rambut. Cuci. Styling.
* EmployeeSeeder. Junior hingga Master.
* ShiftSeeder. Pagi. Siang. Malam jika perlu.
* CommissionRuleSeeder. Misal 30 persen untuk haircut. Atau flat 10 ribu untuk cuci.
* UserSeeder. Admin. Kasir. Stakeholder demo.
* Factory untuk Orders dan OrderItems guna uji laporan.

# 8. Struktur View dan UI Tailwind

## 8.1. Layout Umum

* Navbar kecil dengan role badge. tombol logout. nama kasir.
* Sidebar untuk Admin dan Stakeholder.
* Komponen reusable. Modal. Drawer. Toast. Data table filter. Pagination.

## 8.2. Layar Kasir POS

* Header. Info cash session. jam. shift.
* Kolom kiri. Pencarian jasa. kategori tab. grid kartu jasa.
* Kolom kanan. Keranjang order.

  * Item baris. nama jasa. nama orang person_label. nama karyawan. durasi estimasi.
  * Aksi per item. ganti karyawan. ubah harga. diskon. hapus.
  * Ringkasan. subtotal. diskon. grand total.
  * Tombol pembayaran. pilih metode. masukan jumlah. multi metode. auto hitung kembalian.
* Modal Ubah Harga.

  * Input harga baru. alasan wajib. indikator batas toleransi. kolom otorisasi admin jika melewati batas.
* Pilih Karyawan.

  * List karyawan dengan status tersedia. filter level. indikasi sedang melayani.

## 8.3. Admin

* Master Jasa. tabel dengan filter kategori. tombol tambah. form dengan dasar harga dan komisi.
* Karyawan. tabel. status aktif. level. jadwal dasar.
* Pelanggan. minimal nama dan nomor.
* Shift dan Kas.

  * Buka tutup kas. rekap kas harian. daftar kas kecil.
* Aturan Komisi. form aturan. pratinjau simulasi.
* Laporan Operasional.

  * Penjualan per hari dengan filter tanggal. per jasa. per karyawan. per metode bayar. ekspor CSV atau PDF.
* Log Aktivitas. filter aksi kritis. price_adjustment. order_void. change_variance.

## 8.4. Stakeholder

* Dashboard KPI.

  * Kartu Omzet hari ini. rata rata transaksi. pelanggan baru. utilitas kursi.
  * Grafik tren 30 hari. pendapatan per jam sibuk. jasa terlaris. komisi total.
* Snapshot. tombol kirim email laporan.

## 8.5. Komponen Kecil

* Badge status layanan. Menunggu. Dikerjakan. Selesai.
* Tag person_label. untuk tandai “Anak 1” atau nama panggilan.
* Receipt kecil untuk cetak. nama toko. nomor order. item. karyawan. total. metode bayar. terima kasih.

# 9. Konfigurasi dan Pengaturan

**settings**

* override_price_limit_percent. decimal. contoh 15.
* require_admin_approval_above_limit. boolean.
* print_header_text. string.
* tax_percent jika digunakan.
* enable_promotions. boolean.

Simpan di tabel settings atau file .env lalu mirror ke database untuk runtime edit.

# 10. Event, Listener, dan Observer

* OrderPaid. Listener. GenerateCommission. Push ke dashboard.
* PriceOverridden. Listener. LogActivity. Kirim notifikasi admin jika di atas batas.
* CashSessionClosed. Listener. GenerateCashSummary. kirim snapshot.

Gunakan Model Observers untuk menjaga integritas line_total dan grand_total saat item berubah.

# 11. Pengujian

* Feature test untuk alur POS. tambah item. ubah harga. pilih karyawan. checkout. multi pembayaran.
* Feature test tutup kas. cek variance.
* Feature test komisi. validasi rule prioritas.
* Policy test peran Kasir. Admin. Stakeholder.
* Report test agregasi.

# 12. Migrasi dan Urutan Implementasi

1. Buat proyek Laravel. pasang Tailwind. pasang Breeze jika ingin auth cepat.
2. Buat migrasi untuk semua tabel inti. jalankan seeder.
3. Implementasi model dan relasi. tambahkan casts dan accessor untuk total.
4. Service class Pricing. Commission. Cash.
5. POS Controller dan view. fungsional minimal. tambah item. pilih karyawan. checkout cash.
6. Tambah override harga dengan validasi batas. activity log.
7. Cash session buka tutup. ledger.
8. Komisi otomatis. laporan dasar.
9. Master data CRUD untuk admin.
10. Dashboard untuk stakeholder. snapshot email opsional.

# 13. Rangkuman Flow POS

* Kasir buka shift. buka kas. input uang awal.
* Kasir buat order. tambah item jasa. isi person_label. pilih karyawan per item.
* Jika perlu sesuaikan harga per item. isi alasan. minta otorisasi jika di atas batas.
* Checkout. pilih metode. terima pembayaran. cetak struk.
* Sistem hitung komisi per item. simpan pembayaran dan jejak kas.
* Akhir hari. tutup kas. hitung selisih. simpan rekap.