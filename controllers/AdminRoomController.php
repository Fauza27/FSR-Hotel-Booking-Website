<?php
// AdminRoomController

class AdminRoomController {
    // Mendeklarasikan properti untuk model Room
    private $roomModel;
    
    // Konstruktor yang dijalankan saat objek controller ini dibuat
    public function __construct() {
        // Memuat model Room
        require_once __DIR__ . '/../models/Room.php';
        // Membuat instance dari model Room
        $this->roomModel = new Room();
    }
    
    // Menampilkan daftar semua kamar dengan pagination
    public function index() {
        $limit = 10; // Menentukan jumlah kamar yang ditampilkan per halaman
        // Mengatur halaman default ke 1 jika parameter 'page' tidak ada atau tidak valid
        $page = 1;
        // (mengecek apakah parameter page ada dalam URL  && apakah nilai parameter tersebut merupakan angka yang valid && apakah nilainya lebih besar dari 0)
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0){
            $page = (int)$_GET['page']; // Mengambil nomor halaman dari URL jika valid dan diubah menjadi integer
        }
        //Kegunaan: Menyaring dan memastikan bahwa halaman yang diminta valid dan tidak ada input yang salah. Jika parameter page tidak ada atau tidak valid, halaman pertama (1) akan digunakan sebagai default.

        $offset = ($page - 1) * $limit; // Menghitung offset berdasarkan halaman saat ini
        
        // Menyimpan pencarian dan kategori dari parameter GET (jika ada)
        $search = $_GET['search'] ?? ''; //berarti jika parameter search ada di URL, nilainya akan disalin ke variabel $search; jika tidak ada, maka $search di-set ke string kosong ('').
        $category = $_GET['category'] ?? null; //berarti jika parameter category ada di URL, nilainya akan disalin ke variabel $category; jika tidak ada, maka $category di-set ke null.
        
        // Mengambil semua kategori untuk filter dropdown
        $categories = $this->roomModel->getAllCategories();
        
        // Mendapatkan kamar berdasarkan filter dan paginasi
        $rooms = $this->roomModel->getRoomsPaginated($limit, $offset, $search, $category);
        // $limit untuk membatasi jumlah kamar yang diambil,
        // $offset untuk menentukan dari kamar ke berapa data akan diambil,
        // $search untuk memfilter kamar berdasarkan kata kunci pencarian (nomor kamar, deskripsi, atau kategori),
        // $category untuk memfilter kamar berdasarkan kategori yang dipilih.
        
        // Mendapatkan total jumlah kamar dengan filter yang sama untuk pagination
        $totalRooms = $this->roomModel->getTotalRooms($search, $category);
        // Menghitung jumlah halaman berdasarkan total kamar dan batas per halaman
        $totalPages = ceil($totalRooms / $limit);
        // Fungsi ceil() digunakan untuk membulatkan hasil pembagian totalRooms / limit ke atas, karena jika ada sisa (misalnya, 25 kamar dengan limit 10 per halaman), maka jumlah halaman akan dibulatkan menjadi 3 halaman (bukannya 2 halaman).
        //Variabel $totalPages akan digunakan untuk menampilkan tombol navigasi pagination di halaman admin. Ini memberi tahu berapa banyak halaman yang perlu ditampilkan untuk menavigasi melalui daftar kamar.


        // Memuat tampilan untuk menampilkan daftar kamar
        require __DIR__ . '/../views/admin/rooms/index.php';
    }

    // Menampilkan form untuk menambah kamar baru
    public function create() {
        // Mengambil semua kategori dan fasilitas untuk ditampilkan di form tambah kamar
        $categories = $this->roomModel->getAllCategories();
        $facilities = $this->roomModel->getAllFacilities();
        // Memuat tampilan form tambah kamar
        require __DIR__ . '/../views/admin/rooms/create.php';
    }

    // Proses penyimpanan kamar baru ke database
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            $room_number = trim($_POST['room_number'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            $price_per_night = $_POST['price_per_night'] ?? '';
            $capacity = $_POST['capacity'] ?? '';
            $size_sqm = $_POST['size_sqm'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'available';
            $selectedFacilities = $_POST['facilities'] ?? [];

            // Validasi input form
            if (empty($room_number)) $errors[] = 'Nomor kamar wajib diisi';
            if (empty($category_id)) $errors[] = 'Kategori wajib dipilih';
            if (!is_numeric($price_per_night) || $price_per_night <= 0) $errors[] = 'Harga tidak valid atau harus lebih besar dari 0';
            if (!is_numeric($capacity) || $capacity <= 0) $errors[] = 'Kapasitas tidak valid atau harus lebih besar dari 0';
            if (!is_numeric($size_sqm) || $size_sqm <= 0) $errors[] = 'Ukuran tidak valid atau harus lebih besar dari 0';
            if (empty($description)) $errors[] = 'Deskripsi wajib diisi.';
            if (empty($selectedFacilities)) $errors[] = 'Pilih minimal satu fasilitas.';

            // Validasi gambar
            $uploadedImages = [];
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $imageCount = count($_FILES['images']['name']);
                for ($i = 0; $i < $imageCount; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        // Cek ekstensi dan ukuran
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
                            $errors[] = "Ekstensi file '" . htmlspecialchars($fileName) . "' tidak diizinkan. Hanya: " . implode(', ', ALLOWED_EXTENSIONS);
                        }
                        if ($fileSize > MAX_FILE_SIZE) {
                            $errors[] = "Ukuran file '" . htmlspecialchars($fileName) . "' terlalu besar. Maksimum: " . (MAX_FILE_SIZE / 1024 / 1024) . " MB";
                        }
                        $uploadedImages[] = [
                            'name' => $fileName,
                            'tmp_name' => $fileTmpName,
                            'ext' => $fileExt
                        ];
                    } elseif ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $errors[] = 'Gagal upload gambar: ' . $_FILES['images']['name'][$i];
                    }
                }
                if (empty($uploadedImages)) { // Jika ada file yang dipilih tapi semua gagal
                     $errors[] = 'Minimal satu gambar harus berhasil diupload jika Anda memilih file.';
                }
            } else {
                $errors[] = 'Minimal satu gambar wajib diupload.';
            }


            if (empty($errors)) {
                // Tentukan path upload gambar utama (misalnya gambar pertama)
                $primaryImagePathForRoomTable = null; 
                $savedImagePaths = [];

                // Proses upload gambar setelah validasi
                foreach ($uploadedImages as $index => $img) {
                    // UPLOAD_PATH sudah ada trailing slash dari config.php
                    $uploadDir = UPLOAD_PATH; // Dari config.php
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0777, true)) {
                            $errors[] = "Gagal membuat direktori upload: " . $uploadDir;
                            // Hentikan proses jika direktori tidak bisa dibuat
                            break; 
                        }
                    }
                    $newFileName = 'room_' . time() . '_' . uniqid() . '.' . $img['ext'];
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($img['tmp_name'], $targetPath)) {
                        // Path yang disimpan di DB adalah relatif terhadap root aplikasi atau assets
                        // Misal: 'assets/images/rooms/namafile.jpg'
                        $dbImagePath = str_replace(ROOT_PATH . '/', '', $targetPath);
                        $savedImagePaths[] = [
                            'path' => $dbImagePath,
                            'is_primary' => ($index === 0) // Gambar pertama sebagai primary
                        ];
                        if ($index === 0) {
                            $primaryImagePathForRoomTable = $dbImagePath;
                        }
                    } else {
                        $errors[] = 'Gagal memindahkan file gambar: ' . htmlspecialchars($img['name']);
                    }
                }
                
                // Lanjutkan hanya jika tidak ada error saat pemindahan file
                if (empty($errors)) {
                    $roomData = [
                        'room_number' => $room_number,
                        'category_id' => $category_id,
                        'price_per_night' => $price_per_night,
                        'capacity' => $capacity,
                        'size_sqm' => $size_sqm,
                        'description' => $description,
                        'status' => $status,
                        'primary_image_path' => $primaryImagePathForRoomTable // Untuk kolom image_url di tabel rooms
                    ];

                    $newRoomId = $this->roomModel->addRoom($roomData);

                    if ($newRoomId) {
                        // Simpan fasilitas
                        if (!empty($selectedFacilities)) {
                            if (!$this->roomModel->addRoomFacilities($newRoomId, $selectedFacilities)) {
                                $errors[] = 'Gagal menyimpan fasilitas kamar.';
                                // Pertimbangkan untuk menghapus kamar yang baru dibuat jika fasilitas gagal
                                // $this->roomModel->deleteRoom($newRoomId); // Perlu metode deleteRoom
                            }
                        }

                        // Simpan gambar ke tabel room_images
                        foreach ($savedImagePaths as $imgPathData) {
                            if (!$this->roomModel->addRoomImage($newRoomId, $imgPathData['path'], $imgPathData['is_primary'])) {
                                $errors[] = 'Gagal menyimpan detail gambar: ' . htmlspecialchars($imgPathData['path']);
                            }
                        }
                        
                        // Jika masih ada error setelah mencoba simpan fasilitas/gambar
                        if (!empty($errors)) {
                             // Muat ulang data untuk form
                            $categories = $this->roomModel->getAllCategories();
                            $facilities = $this->roomModel->getAllFacilities();
                            // Hapus kamar yang mungkin sudah terbuat jika ada error signifikan
                            if ($newRoomId) $this->roomModel->deleteRoom($newRoomId); // Hapus kamar jika ada error lanjut
                            require VIEW_PATH . 'admin/rooms/create.php';
                        } else {
                            $_SESSION['success'] = 'Kamar berhasil ditambahkan';
                            header('Location: ' . base_url('admin/rooms')); // Gunakan helper base_url
                            exit;
                        }

                    } else {
                        $errors[] = 'Gagal menambahkan kamar ke database.';
                    }
                }
            }

            // Jika ada error validasi awal atau error saat upload/simpan
            if (!empty($errors)) {
                $categories = $this->roomModel->getAllCategories();
                $facilities = $this->roomModel->getAllFacilities();            
                require VIEW_PATH . 'admin/rooms/create.php'; // Gunakan VIEW_PATH
            }
        } else {
            // Jika bukan POST, redirect atau tampilkan error
            header('Location: ' . base_url('admin/rooms/create'));
            exit;
        }
    }
    
    // Menampilkan form edit kamar berdasarkan ID
    public function edit($id) {
        $room = $this->roomModel->getRoomById($id); // Mengambil data kamar berdasarkan ID
        
        if (!$room) {
            $_SESSION['error'] = 'Kamar tidak ditemukan'; // Jika kamar tidak ada
            header('Location: /admin/rooms'); // Redirect kembali ke halaman daftar kamar
            exit;
        }
        
        // Mengambil kategori dan fasilitas untuk form edit
        $categories = $this->roomModel->getAllCategories();
        $facilities = $this->roomModel->getAllFacilities();
        $roomFacilities = $this->roomModel->getRoomFacilities($id); // Fasilitas yang terkait dengan kamar
        $roomImages = $this->roomModel->getRoomImages($id); // Gambar kamar
        
        require __DIR__ . '/../views/admin/rooms/edit.php'; // Menampilkan form edit kamar
    }

    // Proses update data kamar
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Memastikan form disubmit dengan metode POST
            $errors = []; // Array untuk menampung pesan error
            
            // Mendapatkan data dari form dan menghindari input kosong
            $room_number = trim($_POST['room_number'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            $price_per_night = $_POST['price_per_night'] ?? '';
            $capacity = $_POST['capacity'] ?? '';
            $size_sqm = $_POST['size_sqm'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'available'; // Status default adalah 'available'

            // Validasi input form
            if ($room_number === '') $errors[] = 'Nomor kamar wajib diisi';
            if ($category_id === '') $errors[] = 'Kategori wajib dipilih';
            if (!is_numeric($price_per_night) || $price_per_night <= 0) $errors[] = 'Harga tidak valid';
            if (!is_numeric($capacity) || $capacity <= 0) $errors[] = 'Kapasitas tidak valid';
            if (!is_numeric($size_sqm) || $size_sqm <= 0) $errors[] = 'Ukuran tidak valid';

            // Proses upload gambar baru jika ada
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Menentukan direktori untuk menyimpan gambar
                $uploadDir = __DIR__ . '/../uploads/rooms/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Membuat direktori jika belum ada
                // Mendapatkan ekstensi file gambar
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                // Membuat nama file unik untuk gambar
                $filename = 'room_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $target = $uploadDir . $filename;
                // Memindahkan gambar yang diupload ke direktori yang telah ditentukan
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imagePath = 'uploads/rooms/' . $filename;
                } else {
                    $errors[] = 'Gagal upload gambar'; // Jika gagal upload gambar
                }
            }

            // Menyusun data kamar baru
            $roomData = [
                'room_number' => $room_number,
                'category_id' => $category_id,
                'price_per_night' => $price_per_night,
                'capacity' => $capacity,
                'size_sqm' => $size_sqm,
                'description' => $description,
                'status' => $status
            ];
            // Menambahkan gambar jika ada
            if ($imagePath) {
                $roomData['image'] = $imagePath;
            }

            // Jika tidak ada error, update data kamar di database
            if (empty($errors)) {
                if ($this->roomModel->updateRoom($id, $roomData)) {
                    $_SESSION['success'] = 'Kamar berhasil diperbarui';
                    header('Location: <?= APP_URL ?>/admin/rooms');
                    exit; // Redirect ke halaman daftar kamar setelah berhasil
                } else {
                    $errors[] = 'Gagal memperbarui kamar'; // Jika gagal update
                }
            }

            // Jika ada error, ambil data kamar dan kategori lagi untuk ditampilkan di form
            $room = $this->roomModel->getRoomById($id);
            $categories = $this->roomModel->getAllCategories();
            $facilities = $this->roomModel->getAllFacilities();
            require __DIR__ . '/../views/admin/rooms/edit.php'; // Kembali ke form edit dengan error
        }
    }

    // Menghapus kamar dari database
    public function delete($id) {
        // Memanggil metode untuk menghapus kamar berdasarkan ID
        if ($this->roomModel->deleteRoom($id)) {
            $_SESSION['success'] = 'Kamar berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kamar'; // Jika gagal menghapus
        }
        header('Location: /admin/rooms'); // Redirect kembali ke daftar kamar
        exit;
    }
    
    // Menampilkan detail lengkap kamar
    public function view($id = null) {
        // Jika ID tidak valid
        if (!$id) {
            $_SESSION['error'] = 'ID kamar tidak valid';
            header('Location: ' . APP_URL . '/admin/rooms');
            exit;
        }
        
        // Mengambil data kamar berdasarkan ID
        $room = $this->roomModel->getRoomById($id);
        if (!$room) {
            $_SESSION['error'] = 'Kamar tidak ditemukan';
            header('Location: ' . APP_URL . '/admin/rooms');
            exit;
        }
        
        // Mengambil fasilitas, gambar, dan riwayat pemesanan kamar
        $roomFacilities = $this->roomModel->getRoomFacilities($id);
        $roomImages = $this->roomModel->getRoomImages($id);
        $roomBookings = $this->roomModel->getRoomBookings($id);
        
        // Memuat tampilan untuk menampilkan detail kamar
        require __DIR__ . '/../views/admin/rooms/view.php';
    }
}
