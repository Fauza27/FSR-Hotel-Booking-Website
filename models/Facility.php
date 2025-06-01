<?php
// Facility model

class Facility {
    public $facilityId;
    public $name;
    public $icon;
    public $description;
    private $db;

    public function __construct() {
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
        $this->db->query('SELECT * FROM facilities WHERE facility_id = ?');
        $this->db->bind(1, $id);
        return $this->db->single();
    }

    // Tambah fasilitas
    public function createFacility($data) {
        $this->db->query('INSERT INTO facilities (name, icon, description) VALUES (?, ?, ?)');
        return $this->db->execute([
            $data['name'],
            $data['icon'],
            $data['description'] ?? null
        ]);
    }

    // Update fasilitas
    public function updateFacility($id, $data) {
        $this->db->query('UPDATE facilities SET name = ?, icon = ?, description = ? WHERE facility_id = ?');
        return $this->db->execute([
            $data['name'],
            $data['icon'],
            $data['description'] ?? null,
            $id
        ]);
    }

    // Hapus fasilitas
    public function deleteFacility($id) {
        $this->db->query('DELETE FROM facilities WHERE facility_id = ?');
        return $this->db->execute([$id]);
    }

    // Ambil fasilitas per kamar
    public function getRoomFacilities($roomId) {
        $this->db->query('SELECT f.* FROM facilities f
            JOIN room_facilities rf ON f.facility_id = rf.facility_id
            WHERE rf.room_id = ?');
        $this->db->bind(1, $roomId);
        return $this->db->resultSet();
    }
}
