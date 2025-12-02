# 📋 DOKUMENTASI IMPLEMENTASI MULTISTEP FORM UNTUK BARANG SITAAN

## 🎯 Solusi yang Telah Dibuat

Anda sekarang memiliki sistem **3-tahap pengisian formulir** ketika operator mengklik tombol "Tambah Barang Sitaan":

### **Tahap 1: Informasi Penerbangan**
- **Tab 1 - Pilih Penerbangan**: Operator dapat memilih penerbangan yang sudah ada di database
- **Tab 2 - Penerbangan Baru**: Operator dapat membuat penerbangan baru dengan mengisi:
  - Maskapai (Select dari database)
  - Nomor Penerbangan
  - Bandara Asal
  - Bandara Tujuan
  - Waktu Keberangkatan

### **Tahap 2: Informasi Penumpang**
- **Tab 1 - Pilih Penumpang**: Operator dapat memilih penumpang yang sudah ada di database
- **Tab 2 - Penumpang Baru**: Operator dapat membuat penumpang baru dengan mengisi:
  - Nama Lengkap
  - Nomor Identitas (KTP/Paspor)
  - Nomor Telepon
  - Email
  - Foto Identitas
  - Foto Boarding Pass

### **Tahap 3: Informasi Barang Sitaan**
- Nama Barang
- Gambar Barang
- Kategori Barang
- Jumlah Barang
- Satuan Barang
- Tanggal Penyitaan
- Catatan Tambahan

## 📁 File-File yang Telah Dibuat/Dimodifikasi

### ✅ File Baru:
1. **`app/Filament/Resources/ConfiscatedItems/Schemas/MultiStepConfiscatedItemForm.php`**
   - Mendefinisikan struktur multistep form menggunakan Filament Wizard
   - Mengintegrasikan form penerbangan, penumpang, dan barang sitaan

### ✅ File yang Dimodifikasi:
1. **`app/Filament/Resources/ConfiscatedItems/Pages/CreateConfiscatedItem.php`**
   - Override method `form()` untuk menggunakan `MultiStepConfiscatedItemForm`
   - Menambahkan logika di `mutateFormDataBeforeCreate()` untuk:
     - Membuat Flight baru jika user menggunakan tab "Penerbangan Baru"
     - Membuat Passenger baru jika user menggunakan tab "Penumpang Baru"
     - Mengaitkan Flight dan Passenger ID ke ConfiscatedItem

## 🔄 Alur Kerja

```
Operator Klik "Tambah Barang Sitaan"
    ↓
Multistep Form Terbuka dengan STEP 1
    ↓
[STEP 1] Operator Isi Data Penerbangan (Pilih atau Buat Baru)
    ↓
Klik "Next" atau "Continue"
    ↓
[STEP 2] Operator Isi Data Penumpang (Pilih atau Buat Baru)
    ↓
Klik "Next" atau "Continue"
    ↓
[STEP 3] Operator Isi Data Barang Sitaan
    ↓
Klik "Save" atau "Create"
    ↓
System Membuat:
  1. Flight (jika baru)
  2. Passenger (jika baru)
  3. ConfiscatedItem dengan relasi ke Flight & Passenger
  4. StatusLog dengan status 'RECORDED'
    ↓
Redirect ke halaman list atau detail barang yang baru dibuat
```

## 🔧 Cara Kerja di Backend

### 1. **Form Data yang Diterima:**
```php
[
    'flight_id' => 1,  // atau null jika buat baru
    'new_flight' => [  // hanya ada jika buat penerbangan baru
        'airline_id' => 1,
        'flight_number' => 'GA2150',
        'origin_airport_id' => 1,
        'destination_airport_id' => 2,
        'departure_time' => '2025-11-27 14:30:00',
    ],
    'passenger_id' => 5,  // atau null jika buat baru
    'new_passenger' => [  // hanya ada jika buat penumpang baru
        'full_name' => 'John Doe',
        'identity_number' => '1234567890',
        'phone_number' => '081234567890',
        'email' => 'john@example.com',
        'identity_image_path' => 'path/to/image',
        'boardingpass_image_path' => 'path/to/image',
    ],
    'item_name' => 'Gundam Model',
    'item_image_path' => 'path/to/image',
    'category' => 'prohibited_items',
    'item_quantity' => 1,
    'item_unit' => 'unit',
    'confiscation_date' => '2025-11-27 10:00:00',
    'notes' => 'Barang berbahaya',
]
```

### 2. **Proses di `mutateFormDataBeforeCreate()`:**
```php
// Jika user membuat penerbangan baru
if (!empty($data['new_flight']) && empty($data['flight_id'])) {
    $flight = Flight::create($data['new_flight']);
    $data['flight_id'] = $flight->id;
}

// Jika user membuat penumpang baru
if (!empty($data['new_passenger']) && empty($data['passenger_id'])) {
    $passenger = Passenger::create($data['new_passenger']);
    $data['passenger_id'] = $passenger->id;
}

// Hapus data temporary yang tidak perlu di database
unset($data['new_flight']);
unset($data['new_passenger']);
```

### 3. **Hasil Akhir:**
Data yang tersimpan di `confiscated_items` table:
```php
[
    'passenger_id' => 5,  // ID penumpang yang dipilih/dibuat
    'flight_id' => 1,     // ID penerbangan yang dipilih/dibuat
    'item_name' => 'Gundam Model',
    'item_image_path' => 'path/to/image',
    'category' => 'prohibited_items',
    'item_quantity' => 1,
    'item_unit' => 'unit',
    'confiscation_date' => '2025-11-27 10:00:00',
    'notes' => 'Barang berbahaya',
    'recorded_by_user_id' => 123,  // ID operator yang mencatat
]
```

## ✨ Keunggulan Solusi Ini

✅ **User-Friendly**: Operator tidak perlu berpindah-pindah halaman untuk membuat Flight & Passenger  
✅ **Flexible**: Operator bisa memilih data yang sudah ada atau membuat baru  
✅ **Organized**: Proses dibagi menjadi 3 tahap yang jelas dan logis  
✅ **Automated**: Flight & Passenger otomatis dibuat dan dihubungkan  
✅ **Consistent**: Sama seperti form ConfiscatedItem yang normal, hanya dengan wizard wrapper  

## 🚀 Testing

Untuk memastikan semuanya bekerja:

1. **Login sebagai Operator**
2. **Buka Dashboard**
3. **Klik tombol "Catat Barang Sitaan Baru"** (atau di widget "Tambah Barang Sitaan")
4. **Masuki Step 1**: Pilih penerbangan atau buat baru
5. **Masuki Step 2**: Pilih penumpang atau buat baru
6. **Masuki Step 3**: Isi data barang
7. **Klik Save** - Barang akan tersimpan dengan relasi otomatis ke Flight & Passenger

## 📝 Notes

- Jika operator **memilih penerbangan yang sudah ada**, data `new_flight` tidak akan dikirimkan
- Jika operator **membuat penerbangan baru**, data `flight_id` akan dikosongkan
- Logika yang sama berlaku untuk Passenger
- Status log otomatis dibuat dengan status 'RECORDED' setelah barang disimpan
- Recorded by user ID otomatis diisi dengan ID user yang login

---

**Selesai! Multistep form untuk pencatatan barang sitaan sudah siap digunakan.** ✨
