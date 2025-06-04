<?php
// models/Facility.php

class Facility {
    public $facilityId;
    public $name;
    public $icon;
    public $description;
    private $db;

    public function __construct() {
        // Pastikan config.php sudah di-include sekali saja jika belum,
        // atau autoloading sudah menangani ini.
        // Jika config.php belum di-include di file bootstrap utama (misal index.php),
        // Anda mungkin perlu require_once __DIR__ . '/../config/config.php'; di sini
        // agar konstanta DB_HOST dkk tersedia saat new Database() dipanggil.
        // Namun, karena Database.php sudah menggunakan konstanta tersebut,
        // config.php harus sudah termuat sebelum Database.php.
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    // Ambil semua fasilitas
    public function getAllFacilities() {
        $this->db->query('SELECT * FROM facilities ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Ambil fasilitas berdasarkan ID
    public function getFacilityById($id) {
        $this->db->query('SELECT * FROM facilities WHERE facility_id = :id'); // Menggunakan named placeholder
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Tambah fasilitas
    public function createFacility($data) {
        $this->db->query('INSERT INTO facilities (name, icon, description) VALUES (:name, :icon, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }

    // Update fasilitas
    public function updateFacility($id, $data) {
        $this->db->query('UPDATE facilities SET name = :name, icon = :icon, description = :description WHERE facility_id = :id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Hapus fasilitas
    public function deleteFacility($id) {
        $this->db->query('DELETE FROM facilities WHERE facility_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Ambil fasilitas per kamar
    public function getRoomFacilities($roomId) {
        $this->db->query('SELECT f.* FROM facilities f
            JOIN room_facilities rf ON f.facility_id = rf.facility_id
            WHERE rf.room_id = :room_id');
        $this->db->bind(':room_id', $roomId);
        return $this->db->resultSet();
    }
}
