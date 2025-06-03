<?php
// AdminFacilityController
class AdminFacilityController {
    private $facilityModel;
    public function __construct() {
        require_once __DIR__ . '/../models/Facility.php';
        $this->facilityModel = new Facility();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar fasilitas
    public function index() {
        $facilities = $this->facilityModel->getAllFacilities();
        require __DIR__ . '/../views/admin/facilities/index.php';
    }

    // Form tambah fasilitas
    public function create() {
        require __DIR__ . '/../views/admin/facilities/create.php';
    }

    // Simpan fasilitas baru
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];
            if ($name === '') $errors[] = 'Nama fasilitas wajib diisi';
            if ($icon === '') $errors[] = 'Icon wajib diisi';
            if (empty($errors)) {
                if ($this->facilityModel->addFacility(['name' => $name, 'icon' => $icon, 'description' => $description])) {
                    $_SESSION['success'] = 'Fasilitas berhasil ditambahkan';
                    header('Location: /admin/facilities');
                    exit;
                } else {
                    $errors[] = 'Gagal menambahkan fasilitas';
                }
            }
            require __DIR__ . '/../views/admin/facilities/create.php';
        }
    }

    // Form edit fasilitas
    public function edit($id) {
        $facility = $this->facilityModel->getFacilityById($id);
        require __DIR__ . '/../views/admin/facilities/edit.php';
    }

    // Update data fasilitas
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];
            if ($name === '') $errors[] = 'Nama fasilitas wajib diisi';
            if ($icon === '') $errors[] = 'Icon wajib diisi';
            if (empty($errors)) {
                if ($this->facilityModel->updateFacility($id, ['name' => $name, 'icon' => $icon, 'description' => $description])) {
                    $_SESSION['success'] = 'Fasilitas berhasil diperbarui';
                    header('Location: /admin/facilities');
                    exit;
                } else {
                    $errors[] = 'Gagal memperbarui fasilitas';
                }
            }
            $facility = $this->facilityModel->getFacilityById($id);
            require __DIR__ . '/../views/admin/facilities/edit.php';
        }
    }

    // Hapus fasilitas
    public function delete($id) {
        if ($this->facilityModel->deleteFacility($id)) {
            $_SESSION['success'] = 'Fasilitas berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus fasilitas';
        }
        header('Location: /admin/facilities');
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }
}
