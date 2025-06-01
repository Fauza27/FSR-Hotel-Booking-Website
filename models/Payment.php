<?php

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Create payment
    public function createPayment($data) {
        $this->db->query("
            INSERT INTO payments (booking_id, amount, payment_method, payment_status, transaction_id, payment_date)
            VALUES (:booking_id, :amount, :payment_method, :payment_status, :transaction_id, NOW())
        ");
        
        // Bind values
        //Mengikat nilai dari array $data ke parameter di dalam query SQL untuk mencegah SQL injection.
        $this->db->bind(':booking_id', $data['booking_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':payment_method', $data['payment_method']);
        $this->db->bind(':payment_status', $data['payment_status']);
        $this->db->bind(':transaction_id', $data['transaction_id']);
        
        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    //Tanda : di depan nama parameter seperti :booking_id, :amount, dan lainnya digunakan dalam query SQL untuk menunjukkan parameter placeholder. 
    //Nilai yang sesuai kemudian diikat ke placeholder ini menggunakan metode bind().
    //Penggunaan prepared statements dengan cara ini sangat penting untuk meningkatkan keamanan aplikasi dan mencegah risiko SQL injection.
    
    // Get payment by ID
    public function getPaymentById($paymentId) {
        $this->db->query("
            SELECT * FROM payments
            WHERE payment_id = :payment_id
        ");
        
        $this->db->bind(':payment_id', $paymentId);
        
        return $this->db->single();
    }
    
    // Get payments by booking ID
    //Fungsi ini digunakan untuk mengambil semua pembayaran yang terkait dengan booking_id tertentu.
    public function getPaymentsByBookingId($bookingId) {
        $this->db->query("
            SELECT * FROM payments
            WHERE booking_id = :booking_id
            ORDER BY created_at DESC
        ");
        
        $this->db->bind(':booking_id', $bookingId);
        
        return $this->db->resultSet();
    }
    
    // Update payment status
    //Fungsi ini digunakan untuk memperbarui status pembayaran berdasarkan payment_id.
    public function updatePaymentStatus($paymentId, $status) {
        $this->db->query("
            UPDATE payments
            SET payment_status = :payment_status,
                updated_at = CURRENT_TIMESTAMP
            WHERE payment_id = :payment_id
        ");
        
        // Bind values
        $this->db->bind(':payment_status', $status);
        $this->db->bind(':payment_id', $paymentId);
        
        // Execute
        return $this->db->execute();
    }
    
    // Get payment statistics
    public function getPaymentStatistics() {
        $this->db->query("
            SELECT 
                SUM(amount) as total_revenue,
                COUNT(*) as total_payments,
                COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as completed_payments,
                COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_payments,
                COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_payments,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_payments
            FROM payments
        ");
        
        return $this->db->single();
    }
    
    // Get monthly revenue
    public function getMonthlyRevenue($year) {
        $this->db->query("
            SELECT 
                MONTH(payment_date) as month,
                SUM(amount) as revenue
            FROM payments
            WHERE 
                YEAR(payment_date) = :year AND
                payment_status = 'completed'
            GROUP BY MONTH(payment_date)
            ORDER BY MONTH(payment_date)
        ");
        
        $this->db->bind(':year', $year);
        
        return $this->db->resultSet();
    }
    
    // Get payment methods distribution
    public function getPaymentMethodsDistribution() {
        $this->db->query("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            FROM payments
            WHERE payment_status = 'completed'
            GROUP BY payment_method
            ORDER BY count DESC
        ");
        
        return $this->db->resultSet();
    }

    public function getAllPaymentsFiltered($method = '', $status = '') {
        $query = "SELECT * FROM payments WHERE 1=1"; //WHERE 1=1 adalah kondisi yang selalu benar, yang memungkinkan penambahan kondisi lainnya tanpa mempengaruhi sintaksis query. Ini adalah teknik yang umum digunakan agar query bisa diperluas dengan menambahkan kondisi lebih lanjut tanpa harus memeriksa apakah kondisi pertama perlu diperlakukan secara khusus.
        
        if ($method) {
            $query .= " AND payment_method = :method";
        }
        if ($status) {
            $query .= " AND payment_status = :status";
        }
        
        $this->db->query($query);
        
        if ($method) {
            $this->db->bind(':method', $method);
        }
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        return $this->db->resultSet();
    }

    public function getTotalRevenue() {
        $this->db->query("
            SELECT SUM(amount) as total_revenue 
            FROM payments 
            WHERE payment_status = 'completed'
        ");
        $result = $this->db->single();
        return $result ? $result->total_revenue : 0;
    }

    // Get revenue report berdasarkan periode
    public function getRevenueReport($period = 'monthly') {
        $query = "";
        
        switch($period) {
            case 'daily':
                $query = "
                    SELECT 
                        DATE(payment_date) as date,
                        COUNT(*) as total_payments,
                        SUM(amount) as total_revenue,
                        SUM(CASE WHEN payment_method = 'credit_card' THEN amount ELSE 0 END) as credit_card_revenue,
                        SUM(CASE WHEN payment_method = 'bank_transfer' THEN amount ELSE 0 END) as bank_transfer_revenue,
                        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash_revenue
                    FROM payments
                    WHERE payment_status = 'completed'
                    AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE(payment_date)
                    ORDER BY date DESC
                ";
                break;
                
            case 'monthly':
                $query = "
                    SELECT 
                        YEAR(payment_date) as year,
                        MONTH(payment_date) as month,
                        COUNT(*) as total_payments,
                        SUM(amount) as total_revenue,
                        SUM(CASE WHEN payment_method = 'credit_card' THEN amount ELSE 0 END) as credit_card_revenue,
                        SUM(CASE WHEN payment_method = 'bank_transfer' THEN amount ELSE 0 END) as bank_transfer_revenue,
                        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash_revenue
                    FROM payments
                    WHERE payment_status = 'completed'
                    AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY YEAR(payment_date), MONTH(payment_date)
                    ORDER BY year DESC, month DESC
                ";
                break;
                
            case 'yearly':
                $query = "
                    SELECT 
                        YEAR(payment_date) as year,
                        COUNT(*) as total_payments,
                        SUM(amount) as total_revenue,
                        SUM(CASE WHEN payment_method = 'credit_card' THEN amount ELSE 0 END) as credit_card_revenue,
                        SUM(CASE WHEN payment_method = 'bank_transfer' THEN amount ELSE 0 END) as bank_transfer_revenue,
                        SUM(CASE WHEN payment_method = 'cash' THEN amount ELSE 0 END) as cash_revenue
                    FROM payments
                    WHERE payment_status = 'completed'
                    GROUP BY YEAR(payment_date)
                    ORDER BY year DESC
                ";
                break;
                
            case 'all':
                $query = "
                    SELECT 
                        p.*,
                        b.room_id,
                        b.check_in_date,
                        b.check_out_date,
                        u.full_name as user_name,
                        r.room_number
                    FROM payments p
                    LEFT JOIN bookings b ON p.booking_id = b.booking_id
                    LEFT JOIN users u ON b.user_id = u.user_id
                    LEFT JOIN rooms r ON b.room_id = r.room_id
                    ORDER BY p.payment_date DESC
                ";
                break;
        }
        
        $this->db->query($query);
        return $this->db->resultSet();
    }

    // Get payment statistics
    public function getPaymentStats() {
        $this->db->query("
            SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed_payments,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_payments,
                SUM(CASE WHEN payment_status = 'refunded' THEN 1 ELSE 0 END) as refunded_payments,
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as completed_amount,
                AVG(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as avg_payment_amount
            FROM payments
        ");
        return $this->db->single();
    }

    // Get payment method distribution
    public function getPaymentMethodDistribution() {
        $this->db->query("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                ROUND((COUNT(*) / (SELECT COUNT(*) FROM payments WHERE payment_status = 'completed')) * 100, 2) as percentage
            FROM payments
            WHERE payment_status = 'completed'
            GROUP BY payment_method
            ORDER BY count DESC
        ");
        return $this->db->resultSet();
    }

    // Get revenue trend untuk grafik
    public function getRevenueTrend($days = 30) {
        $this->db->query("
            SELECT 
                DATE(payment_date) as date, 
                SUM(amount) as revenue
            FROM payments
            WHERE payment_status = 'completed'
            AND payment_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(payment_date)
            ORDER BY date ASC
        ");
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }
}