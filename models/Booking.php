<?php
// models/Booking.php

class Booking {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Create booking
    public function createBooking($data) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Insert booking
            $this->db->query("
                INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, 
                                    total_price, adults, children, status, identity_file)
                VALUES (:user_id, :room_id, :check_in_date, :check_out_date, 
                        :total_price, :adults, :children, :status, :identity_file)
            ");
            
            // Bind values
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':room_id', $data['room_id']);
            $this->db->bind(':check_in_date', $data['check_in_date']);
            $this->db->bind(':check_out_date', $data['check_out_date']);
            $this->db->bind(':total_price', $data['total_price']);
            $this->db->bind(':adults', $data['adults']);
            $this->db->bind(':children', $data['children']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':identity_file', $data['identity_file']);
            
            // Execute
            $this->db->execute();
            
            // Get the booking ID
            $bookingId = $this->db->lastInsertId();
            
            // Update room status if booking is confirmed
            if($data['status'] == 'confirmed') {
                $this->db->query("
                    UPDATE rooms
                    SET status = 'occupied'
                    WHERE room_id = :room_id
                ");
                
                $this->db->bind(':room_id', $data['room_id']);
                $this->db->execute();
            }
            
            // Commit transaction
            $this->db->endTransaction();
            
            return $bookingId;
            
        } catch(Exception $e) {
            // Rollback transaction
            $this->db->cancelTransaction();
            
            return false;
        }
    }
    
    // Get booking by ID
    public function getBookingById($bookingId) {
        $this->db->query("
            SELECT b.*, r.room_number, r.price_per_night, r.image_url,
                   u.full_name as user_name, u.email as user_email, u.phone as user_phone
            FROM bookings b
            JOIN rooms r ON b.room_id = r.room_id
            JOIN users u ON b.user_id = u.user_id
            WHERE b.booking_id = :booking_id
        ");
        
        $this->db->bind(':booking_id', $bookingId);
        
        return $this->db->single();
    }
    
    // Get user bookings
    public function getUserBookings($userId) {
        $this->db->query("
            SELECT b.*, r.room_number, r.price_per_night, r.image_url, c.name as category_name
            FROM bookings b
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_categories c ON r.category_id = c.category_id
            WHERE b.user_id = :user_id
            ORDER BY b.created_at DESC
        ");
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
    
    // Update booking status
    public function updateBookingStatus($bookingId, $status) {
        $this->db->beginTransaction();
        
        try {
            // Get the booking details first
            $this->db->query("SELECT * FROM bookings WHERE booking_id = :booking_id");
            $this->db->bind(':booking_id', $bookingId);
            $booking = $this->db->single();
            
            // Update booking status
            $this->db->query("
                UPDATE bookings
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE booking_id = :booking_id
            ");
            
            $this->db->bind(':status', $status);
            $this->db->bind(':booking_id', $bookingId);
            $this->db->execute();
            
            // Update room status depending on booking status
            if($status == 'confirmed') {
                $this->db->query("
                    UPDATE rooms
                    SET status = 'occupied'
                    WHERE room_id = :room_id
                ");
                
                $this->db->bind(':room_id', $booking->room_id);
                $this->db->execute();
            } 
            else if($status == 'cancelled' || $status == 'completed') {
                $this->db->query("
                    UPDATE rooms
                    SET status = 'available'
                    WHERE room_id = :room_id
                ");
                
                $this->db->bind(':room_id', $booking->room_id);
                $this->db->execute();
            }
            
            // Commit transaction
            $this->db->endTransaction();
            
            return true;
            
        } catch(Exception $e) {
            // Rollback transaction
            $this->db->cancelTransaction();
            
            return false;
        }
    }
    
    // Calculate nights between two dates
    public function calculateNights($checkIn, $checkOut) {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $interval = $checkInDate->diff($checkOutDate);
        
        return $interval->days;
    }
    
    // Calculate total price
    public function calculateTotalPrice($roomPrice, $nights) {
        return $roomPrice * $nights;
    }
    
    // Get active bookings by room id
    public function getActiveBookingsByRoomId($roomId) {
        $this->db->query("
            SELECT * FROM bookings
            WHERE room_id = :room_id
            AND status IN ('pending', 'confirmed')
        ");
        $this->db->bind(':room_id', $roomId);
        return $this->db->resultSet();
    }
    
    // Get total number of bookings
    public function getTotalBookings() {
        $this->db->query("SELECT COUNT(*) as total FROM bookings");
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }

    // Get pending bookings count
    public function getPendingBookings() {
        $this->db->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }

    // Get recent bookings (limit N)
    public function getRecentBookings($limit = 5) {
        $this->db->query("SELECT b.*, u.full_name, r.room_number FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN rooms r ON b.room_id = r.room_id ORDER BY b.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', (int)$limit);
        return $this->db->resultSet();
    }

    // Get monthly revenue for the current year (array of 12 months)
    public function getMonthlyRevenue() {
        $this->db->query("SELECT MONTH(check_in_date) as month, SUM(total_price) as revenue FROM bookings WHERE status = 'confirmed' AND YEAR(check_in_date) = YEAR(CURDATE()) GROUP BY MONTH(check_in_date)");
        $results = $this->db->resultSet();
        $revenue = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $revenue[(int)$row->month] = (float)$row->revenue;
        }
        return $revenue;
    }

    // Get all bookings by user ID (for admin user management)
    public function getBookingsByUserId($userId) {
        $this->db->query("SELECT * FROM bookings WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function getAllBookingsFiltered($filters = []) {
    $query = "SELECT b.*, r.room_number, r.price_per_night, u.full_name as user_name 
              FROM bookings b 
              JOIN rooms r ON b.room_id = r.room_id 
              JOIN users u ON b.user_id = u.user_id";
    
    $conditions = [];
    $params = [];

    if (!empty($filters['status'])) {
        $conditions[] = "b.status = :status";
        $params[':status'] = $filters['status'];
    }
    if (!empty($filters['date_start'])) {
        $conditions[] = "b.check_in_date >= :date_start";
        $params[':date_start'] = $filters['date_start'];
    }
    if (!empty($filters['date_end'])) {
        $conditions[] = "b.check_out_date <= :date_end";
        $params[':date_end'] = $filters['date_end'];
    }

    if ($conditions) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY b.created_at DESC";

    $this->db->query($query);
    
    foreach ($params as $key => $value) {
        $this->db->bind($key, $value);
    }

    return $this->db->resultSet();
}

    public function getBookingReport($period = 'daily') {
        $query = "";
        
        switch($period) {
            case 'daily':
                $query = "
                    SELECT DATE(created_at) as date, 
                           COUNT(*) as total_bookings,
                           SUM(total_price) as total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                    FROM bookings
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date DESC
                ";
                break;
                
            case 'monthly':
                $query = "
                    SELECT YEAR(created_at) as year,
                           MONTH(created_at) as month,
                           COUNT(*) as total_bookings,
                           SUM(total_price) as total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                    FROM bookings
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY YEAR(created_at), MONTH(created_at)
                    ORDER BY year DESC, month DESC
                ";
                break;
                
            case 'yearly':
                $query = "
                    SELECT YEAR(created_at) as year,
                           COUNT(*) as total_bookings,
                           SUM(total_price) as total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                    FROM bookings
                    GROUP BY YEAR(created_at)
                    ORDER BY year DESC
                ";
                break;
                
            case 'all':
                $query = "
                    SELECT b.*, r.room_number, u.full_name as user_name,
                           p.payment_status, p.payment_method
                    FROM bookings b
                    LEFT JOIN rooms r ON b.room_id = r.room_id
                    LEFT JOIN users u ON b.user_id = u.user_id
                    LEFT JOIN payments p ON b.booking_id = p.booking_id
                    ORDER BY b.created_at DESC
                ";
                break;
        }
        
        $this->db->query($query);
        return $this->db->resultSet();
    }

    // Get total revenue dari bookings yang confirmed
    public function getTotalRevenue() {
        $this->db->query("
            SELECT SUM(total_price) as total_revenue 
            FROM bookings 
            WHERE status = 'confirmed'
        ");
        $result = $this->db->single();
        return $result ? $result->total_revenue : 0;
    }

    // Get booking statistics untuk dashboard
    public function getBookingStats() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                SUM(total_price) as total_revenue,
                AVG(DATEDIFF(check_out_date, check_in_date)) as avg_stay_duration
            FROM bookings
        ");
        return $this->db->single();
    }

    // Get booking trend untuk grafik
    public function getBookingTrend($days = 30) {
        $this->db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM bookings
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }

}