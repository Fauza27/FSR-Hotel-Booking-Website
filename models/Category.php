<?php
// Category model

class Category {
    public $categoryId;
    public $name;
    public $description;
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    // Ambil semua kategori
    public function getAllCategories() {
        $this->db->query('SELECT * FROM room_categories ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Ambil kategori berdasarkan ID
    public function getCategoryById($id) {
        $this->db->query('SELECT * FROM room_categories WHERE category_id = :category_id');
        $this->db->bind(':category_id', $id);
        return $this->db->single();
    }

    // Tambah kategori baru
    public function createCategory($data) {
        $this->db->query('INSERT INTO room_categories (name, description) VALUES (:name, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }

    // Update kategori
    public function updateCategory($id, $data) {
        $this->db->query('UPDATE room_categories SET name = :name, description = :description WHERE category_id = :category_id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':category_id', $id);
        return $this->db->execute();
    }

    // Hapus kategori
    public function deleteCategory($id) {
        $this->db->query('DELETE FROM room_categories WHERE category_id = :category_id');
        $this->db->bind(':category_id', $id);
        return $this->db->execute();
    }

    // Ambil kamar per kategori - DIPERBAIKI
    public function getRoomsByCategory($categoryId) {
        $this->db->query('SELECT * FROM rooms WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        return $this->db->resultSet();
    }

    // TAMBAHAN: Method untuk menghitung jumlah kamar per kategori
    public function getRoomCountByCategory($categoryId) {
        $this->db->query('SELECT COUNT(*) as count FROM rooms WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        $result = $this->db->single();
        return $result ? $result->count : 0;
    }

    // TAMBAHAN: Method untuk mendapatkan semua kategori dengan jumlah kamar
    public function getAllCategoriesWithRoomCount() {
        $this->db->query('
            SELECT rc.*, COUNT(r.room_id) as room_count 
            FROM room_categories rc 
            LEFT JOIN rooms r ON rc.category_id = r.category_id 
            GROUP BY rc.category_id 
            ORDER BY rc.name ASC
        ');
        return $this->db->resultSet();
    }
}
