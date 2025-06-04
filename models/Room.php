<?php

// Konstruktor: Membuat instance dari kelas Database untuk mengatur koneksi database.
// Setiap method: Masing-masing memiliki tujuan yang berbeda, baik itu untuk mengambil data kamar, fasilitas, kategori, memeriksa ketersediaan kamar, atau melakukan query terkait lainnya.
// Bind Parameter: Digunakan untuk mencegah SQL Injection dengan mengikat nilai parameter ke dalam query yang akan dieksekusi.
// Query: Setiap metode menjalankan query SQL menggunakan metode query() dari kelas Database untuk mengambil data terkait.

class Room {
    private $db;
    
    // Konstruktor kelas Room, yang akan membuat instance Database
    public function __construct() {
        $this->db = new Database(); // Membuat koneksi ke database
    }
    
    // Mendapatkan semua kamar yang statusnya 'available' (tersedia)
    public function getAllRooms() {
        $this->db->query("
            SELECT r.*, c.name as category_name, ri.image_url as primary_image_url
            FROM rooms r
            JOIN room_categories c ON r.category_id = c.category_id
            LEFT JOIN room_images ri ON r.room_id = ri.room_id AND ri.is_primary = 1
            WHERE r.status = 'available'
            ORDER BY r.price_per_night ASC
        ");
        return $this->db->resultSet();
    }
    
    // Mendapatkan kamar berdasarkan kategori tertentu
    public function getRoomsByCategory($categoryId) {
        // Menjalankan query untuk memilih kamar berdasarkan kategori yang diberikan
        $this->db->query("
            SELECT r.*, c.name as category_name 
            FROM rooms r
            JOIN room_categories c ON r.category_id = c.category_id
            WHERE r.category_id = :category_id AND r.status = 'available'
            ORDER BY r.price_per_night ASC
        ");
        
        // Mengikat parameter kategori
        $this->db->bind(':category_id', $categoryId);
        
        // Mengembalikan hasil query
        return $this->db->resultSet();
    }
    
    // Mendapatkan kamar berdasarkan ID kamar
    public function getRoomById($roomId) {
        // Menjalankan query untuk memilih kamar berdasarkan ID yang diberikan
        $this->db->query("
            SELECT r.*, c.name as category_name, c.description as category_description
            FROM rooms r
            JOIN room_categories c ON r.category_id = c.category_id
            WHERE r.room_id = :room_id
        ");
        
        // Mengikat parameter ID kamar
        $this->db->bind(':room_id', $roomId);
        
        // Mengembalikan satu hasil (kamar tunggal)
        return $this->db->single();
    }
    
    // Mendapatkan semua kategori kamar
    public function getAllCategories() {
        // Menjalankan query untuk memilih semua kategori kamar
        $this->db->query("SELECT * FROM room_categories ORDER BY name ASC");
        
        // Mengembalikan hasil query sebagai array objek
        return $this->db->resultSet();
    }

    // Mendapatkan semua fasilitas yang ada di hotel
    public function getAllFacilities() {
        // Menjalankan query untuk memilih semua fasilitas yang ada
        $this->db->query("SELECT * FROM facilities ORDER BY name ASC");
        
        // Mengembalikan hasil query sebagai array objek
        return $this->db->resultSet();
    }

    // Mendapatkan fasilitas yang dimiliki oleh sebuah kamar
    public function getRoomFacilities($roomId) {
        // Menjalankan query untuk memilih fasilitas kamar berdasarkan ID kamar
        $this->db->query("
            SELECT f.* 
            FROM room_facilities rf
            JOIN facilities f ON rf.facility_id = f.facility_id
            WHERE rf.room_id = :room_id
        ");
        
        // Mengikat parameter ID kamar
        $this->db->bind(':room_id', $roomId);
        
        // Mengembalikan hasil query berupa fasilitas yang terkait dengan kamar
        return $this->db->resultSet();
    }

    // Mendapatkan gambar-gambar yang terkait dengan sebuah kamar
    public function getRoomImages($roomId) {
        // Menjalankan query untuk memilih gambar kamar berdasarkan ID kamar
        $this->db->query("SELECT * FROM room_images WHERE room_id = :room_id ORDER BY is_primary DESC, image_id ASC");
        
        // Mengikat parameter ID kamar
        $this->db->bind(':room_id', $roomId);
        
        // Mengembalikan hasil query berupa gambar yang terkait dengan kamar
        return $this->db->resultSet();
    }

    public function addRoom($data) {
        $this->db->query("INSERT INTO rooms (room_number, category_id, price_per_night, capacity, size_sqm, description, status, image_url) 
                          VALUES (:room_number, :category_id, :price_per_night, :capacity, :size_sqm, :description, :status, :image_url)");
        
        $this->db->bind(':room_number', $data['room_number']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':price_per_night', $data['price_per_night']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':size_sqm', $data['size_sqm']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        // Untuk image_url di tabel rooms, kita bisa simpan gambar utama atau biarkan null jika gambar dikelola di room_images
        // Untuk saat ini, kita asumsikan gambar utama disimpan di room_images dan ini bisa NULL atau path ke gambar utama pertama
        $this->db->bind(':image_url', $data['primary_image_path'] ?? null); 

        if ($this->db->execute()) {
            return $this->db->lastInsertId(); // Mengembalikan ID kamar yang baru saja ditambahkan
        } else {
            return false;
        }
    }

    public function addRoomFacilities($roomId, $facilityIds) {
        if (empty($facilityIds)) {
            return true; // Tidak ada fasilitas untuk ditambahkan
        }
        // Hapus fasilitas lama jika ada (berguna untuk update, tapi untuk create bisa diskip jika pasti belum ada)
        // $this->db->query("DELETE FROM room_facilities WHERE room_id = :room_id");
        // $this->db->bind(':room_id', $roomId);
        // $this->db->execute();

        $this->db->beginTransaction(); // Mulai transaksi
        try {
            foreach ($facilityIds as $facilityId) {
                $this->db->query("INSERT INTO room_facilities (room_id, facility_id) VALUES (:room_id, :facility_id)");
                $this->db->bind(':room_id', $roomId);
                $this->db->bind(':facility_id', $facilityId);
                if (!$this->db->execute()) {
                    throw new Exception("Gagal menambahkan fasilitas ID: " . $facilityId);
                }
            }
            $this->db->endTransaction(); // Commit transaksi
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback jika ada error
            error_log("Error adding room facilities: " . $e->getMessage());
            return false;
        }
    }

    public function addRoomImage($roomId, $imageUrl, $isPrimary = 0) {
        $this->db->query("INSERT INTO room_images (room_id, image_url, is_primary) VALUES (:room_id, :image_url, :is_primary)");
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':image_url', $imageUrl); // Ini adalah path relatif dari APP_URL
        $this->db->bind(':is_primary', $isPrimary);
        return $this->db->execute();
    }

    public function deleteRoom($roomId) {
        $this->db->beginTransaction();
        try {
            // Hapus dari tabel junction dulu
            $this->db->query("DELETE FROM room_facilities WHERE room_id = :room_id");
            $this->db->bind(':room_id', $roomId);
            $this->db->execute();

            $this->db->query("DELETE FROM room_images WHERE room_id = :room_id");
            $this->db->bind(':room_id', $roomId);
            $this->db->execute();
            
            // Hapus dari tabel bookings (jika diperlukan, tergantung aturan bisnis, atau set ON DELETE CASCADE)
            // $this->db->query("DELETE FROM bookings WHERE room_id = :room_id");
            // $this->db->bind(':room_id', $roomId);
            // $this->db->execute();

            // Akhirnya hapus kamar
            $this->db->query("DELETE FROM rooms WHERE room_id = :room_id");
            $this->db->bind(':room_id', $roomId);
            $this->db->execute();

            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            error_log("Error deleting room: " . $e->getMessage());
            return false;
        }
    }
    
    // Mengecek ketersediaan kamar berdasarkan tanggal check-in dan check-out
    public function checkAvailability($roomId, $checkIn, $checkOut) {
        // Menjalankan query untuk memeriksa apakah ada pemesanan yang bertentangan dengan rentang tanggal yang diberikan
        $this->db->query("
            SELECT COUNT(*) as booking_count
            FROM bookings
            WHERE room_id = :room_id
            AND status IN ('pending', 'confirmed')
            AND (
                (check_in_date <= :check_in AND check_out_date > :check_in)
                OR
                (check_in_date < :check_out AND check_out_date >= :check_out)
                OR
                (check_in_date >= :check_in AND check_out_date <= :check_out)
            )
        ");
        
        // Mengikat parameter ID kamar dan tanggal check-in/check-out
        $this->db->bind(':room_id', $roomId);
        $this->db->bind(':check_in', $checkIn);
        $this->db->bind(':check_out', $checkOut);
        
        // Mengambil hasil query (jumlah pemesanan yang bertentangan)
        $result = $this->db->single();
        
        // Jika tidak ada pemesanan yang bertentangan, mengembalikan true (tersedia)
        return $result->booking_count == 0;
    }
    
    // Mencari kamar yang tersedia berdasarkan tanggal check-in, check-out, dan jumlah tamu
    public function searchAvailableRooms($checkIn, $checkOut, $guests) {
        // Menjalankan query untuk mencari kamar yang tersedia sesuai dengan filter yang diberikan
        $this->db->query("
            SELECT r.*, c.name as category_name 
            FROM rooms r
            JOIN room_categories c ON r.category_id = c.category_id
            WHERE r.status = 'available'
            AND r.capacity >= :guests
            AND r.room_id NOT IN (
                SELECT b.room_id
                FROM bookings b
                WHERE b.status IN ('pending', 'confirmed')
                AND (
                    (b.check_in_date <= :check_in AND b.check_out_date > :check_in)
                    OR
                    (b.check_in_date < :check_out AND b.check_out_date >= :check_out)
                    OR
                    (b.check_in_date >= :check_in AND b.check_out_date <= :check_out)
                )
            )
            ORDER BY r.price_per_night ASC
        ");
        
        // Mengikat parameter jumlah tamu, tanggal check-in, dan tanggal check-out
        $this->db->bind(':guests', $guests);
        $this->db->bind(':check_in', $checkIn);
        $this->db->bind(':check_out', $checkOut);
        
        // Mengembalikan hasil query berupa daftar kamar yang tersedia
        return $this->db->resultSet();
    }

    // Mendapatkan jumlah total kamar dengan filter pencarian dan kategori
    public function getTotalRooms($search = '', $category = null) {
        try {
            // Menyusun query untuk menghitung total kamar berdasarkan filter pencarian dan kategori
            $sql = "SELECT COUNT(*) as total 
                    FROM rooms r 
                    INNER JOIN room_categories c ON r.category_id = c.category_id 
                    WHERE 1=1";

            $params = [];
            
            // Menambahkan filter pencarian
            if (!empty($search)) {
                $sql .= " AND (r.room_number LIKE :search 
                          OR r.description LIKE :search 
                          OR c.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            // Menambahkan filter kategori
            if (!empty($category)) {
                $sql .= " AND r.category_id = :category_id";
                $params[':category_id'] = (int)$category;
            }
            
            // Menjalankan query untuk mendapatkan hasil total kamar
            $this->db->query($sql);
            
            // Mengikat parameter-parameter pencarian
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            
            // Mengambil hasil query (jumlah total kamar)
            $result = $this->db->single();
            
            // Mengembalikan total kamar atau 0 jika tidak ditemukan
            return $result ? $result->total : 0;
        } catch (Exception $e) {
            // Menangani error jika terjadi kesalahan dalam query
            error_log("Error in getTotalRooms: " . $e->getMessage());
            return 0;
        }
    }
    
    // Mendapatkan statistik ketersediaan kamar untuk dashboard
    public function getRoomAvailabilityStats() {
        // Menjalankan query untuk menghitung jumlah kamar berdasarkan status
        $this->db->query("SELECT status, COUNT(*) as count FROM rooms GROUP BY status");
        
        // Mengambil hasil query
        $results = $this->db->resultSet();
        
        // Menyusun statistik berdasarkan hasil
        $stats = [
            'available' => 0,
            'occupied' => 0,
            'maintenance' => 0
        ];
        
        // Mengisi statistik sesuai dengan status kamar yang ditemukan
        foreach ($results as $row) {
            if (isset($stats[$row->status])) {
                $stats[$row->status] = (int)$row->count;
            }
        }
        
        // Mengembalikan statistik
        return $stats;
    }

    // Mendapatkan kamar dengan pagination untuk panel admin
    public function getRoomsPaginated($limit = 10, $offset = 0, $search = '', $category = null) {
        try {
            // Menyusun query untuk mengambil kamar dengan pagination
            $sql = "SELECT r.*, c.name as category_name 
                    FROM rooms r 
                    INNER JOIN room_categories c ON r.category_id = c.category_id 
                    WHERE 1=1";
            $params = [];
            
            // Menambahkan filter pencarian
            if (!empty($search)) {
                $sql .= " AND (r.room_number LIKE :search 
                              OR r.description LIKE :search 
                              OR c.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            // Menambahkan filter kategori
            if (!empty($category)) {
                $sql .= " AND r.category_id = :category_id";
                $params[':category_id'] = (int)$category;
            }
            
            // Menambahkan limit dan offset untuk pagination
            $sql .= " ORDER BY r.room_number ASC LIMIT :limit OFFSET :offset";
            
            // Menjalankan query
            $this->db->query($sql);
            
            // Mengikat semua parameter yang digunakan dalam query
            foreach ($params as $param => $value) {
                $this->db->bind($param, $value);
            }
            
            // Mengikat parameter limit dan offset
            $this->db->bind(':limit', (int)$limit);
            $this->db->bind(':offset', (int)$offset);
            
            // Mengembalikan hasil query sebagai daftar kamar yang dipaginasi
            return $this->db->resultSet();
        } catch (Exception $e) {
            // Menangani error jika terjadi kesalahan dalam query
            error_log("Error in getRoomsPaginated: " . $e->getMessage());
            return [];
        }
    }
    
    // Mendapatkan riwayat pemesanan kamar
    public function getRoomBookings($roomId) {
        // Menjalankan query untuk mendapatkan riwayat pemesanan kamar
        $this->db->query("
            SELECT b.*, u.full_name as user_name
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.user_id
            WHERE b.room_id = :room_id
            ORDER BY b.created_at DESC
        ");
        
        // Mengikat parameter ID kamar
        $this->db->bind(':room_id', $roomId);
        
        // Mengembalikan hasil query sebagai riwayat pemesanan kamar
        return $this->db->resultSet();
    }

    public function updateRoom($id, $data) {
    // Menyusun query untuk memperbarui data kamar
    $sql = "UPDATE rooms SET 
            room_number = :room_number, 
            category_id = :category_id,
            price_per_night = :price_per_night,
            capacity = :capacity,
            size_sqm = :size_sqm,
            description = :description,
            status = :status";

    // Jika ada gambar baru, update gambar
    if (isset($data['image'])) {
        $sql .= ", image = :image";
    }

    $sql .= " WHERE room_id = :room_id"; // Tentukan kondisi WHERE untuk ID kamar

    // Menyiapkan query untuk dijalankan
    $this->db->query($sql);

    // Mengikat parameter
    $this->db->bind(':room_number', $data['room_number']);
    $this->db->bind(':category_id', $data['category_id']);
    $this->db->bind(':price_per_night', $data['price_per_night']);
    $this->db->bind(':capacity', $data['capacity']);
    $this->db->bind(':size_sqm', $data['size_sqm']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':room_id', $id);

    // Jika ada gambar baru, tambahkan parameter gambar
    if (isset($data['image'])) {
        $this->db->bind(':image', $data['image']);
    }

    // Menjalankan query dan mengembalikan hasilnya
    return $this->db->execute();
}

    // Get room occupancy statistics
    public function getRoomOccupancyStats() {
        // Periode laporan (misalnya 30 hari terakhir)
        // Anda bisa membuat ini dinamis jika diperlukan
        $reportStartDate = date('Y-m-d', strtotime('-90 days'));
        $reportEndDate = date('Y-m-d');
        $reportEndDatePlusOne = date('Y-m-d', strtotime($reportEndDate . ' +1 day')); // Untuk DATEDIFF inklusif

        $this->db->query("
           SELECT 
                r.room_id,
                r.room_number,
                r.price_per_night,
                r.status,
                rc.name as category_name,
                COUNT(b.booking_id) as total_bookings,
                COALESCE(SUM(b.total_price), 0) as total_revenue, -- Diperbaiki
                COALESCE(AVG(DATEDIFF(b.check_out_date, b.check_in_date)), 0) as avg_stay_duration, -- Diperbaiki
                -- Pastikan pembagian dengan float dan COALESCE untuk hasil akhir
                COALESCE(ROUND((COUNT(b.booking_id) / 30.0) * 100, 2), 0) as occupancy_percentage -- Diperbaiki
            FROM rooms r
            LEFT JOIN room_categories rc ON r.category_id = rc.category_id
            LEFT JOIN bookings b ON r.room_id = b.room_id 
                AND b.status = 'confirmed'
                AND b.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            -- Pastikan semua kolom non-agregat ada di GROUP BY
            GROUP BY r.room_id, r.room_number, r.price_per_night, r.status, rc.name 
            ORDER BY total_revenue DESC
        ");
        
        $this->db->bind(':report_start_date', $reportStartDate);
        $this->db->bind(':report_end_date_plus_one', $reportEndDatePlusOne);
        
        return $this->db->resultSet();
    }

    // Get room occupancy report dengan detail
    public function getRoomOccupancyReport() {
        $this->db->query("
            SELECT 
                r.room_id,
                r.room_number,
                r.price_per_night,
                r.status,
                rc.name as category_name,
                COUNT(b.booking_id) as total_bookings,
                SUM(b.total_price) as total_revenue,
                AVG(DATEDIFF(b.check_out_date, b.check_in_date)) as avg_stay_duration,
                ROUND((COUNT(b.booking_id) / 30) * 100, 2) as occupancy_percentage
            FROM rooms r
            LEFT JOIN room_categories rc ON r.category_id = rc.category_id
            LEFT JOIN bookings b ON r.room_id = b.room_id 
                AND b.status = 'confirmed'
                AND b.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY r.room_id
            ORDER BY total_revenue DESC
        ");
        return $this->db->resultSet();
    }

    // Get room performance berdasarkan kategori
    public function getRoomPerformanceByCategory() {
        $this->db->query("
            SELECT 
                rc.name as category_name,
                COUNT(DISTINCT r.room_id) as total_rooms,
                COUNT(b.booking_id) as total_bookings,
                SUM(b.total_price) as total_revenue,
                AVG(b.total_price) as avg_booking_value
            FROM room_categories rc
            LEFT JOIN rooms r ON rc.category_id = r.category_id
            LEFT JOIN bookings b ON r.room_id = b.room_id AND b.status = 'confirmed'
            GROUP BY rc.category_id
            ORDER BY total_revenue DESC
        ");
        return $this->db->resultSet();
    }

    // Get room availability forecast
    public function getRoomAvailabilityForecast($days = 30) {
        $this->db->query("
            SELECT 
                DATE_ADD(CURDATE(), INTERVAL seq.seq DAY) as date,
                (SELECT COUNT(*) FROM rooms) - 
                (SELECT COUNT(DISTINCT r.room_id) 
                 FROM rooms r 
                 JOIN bookings b ON r.room_id = b.room_id 
                 WHERE b.status IN ('confirmed', 'pending')
                 AND DATE_ADD(CURDATE(), INTERVAL seq.seq DAY) BETWEEN b.check_in_date AND b.check_out_date
                ) as available_rooms
            FROM (
                SELECT 0 as seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
                UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11
                UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
                UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19
                UNION ALL SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23
                UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27
                UNION ALL SELECT 28 UNION ALL SELECT 29
            ) seq
            WHERE seq.seq < :days
        ");
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }


}
