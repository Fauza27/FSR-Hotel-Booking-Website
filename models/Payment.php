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
        return $result ? (float)$result->total_revenue : 0;
    } 

    // Get revenue report berdasarkan periode
    // Get revenue report berdasarkan periode dan rentang tanggal
    public function getRevenueReport($period = 'monthly', $startDate = null, $endDate = null) {
        $response = (object) [
            'chartData' => [],
            'breakdownData' => [],
            'currentPeriodRevenue' => 0,
            'previousPeriodRevenue' => 0,
            'periodLabel' => '', // Untuk label di grafik (Harian, Bulanan, Tahunan)
            'actualStartDate' => null, // Untuk menampilkan di form filter
            'actualEndDate' => null,   // Untuk menampilkan di form filter
        ];

        $whereClauses = ["p.payment_status = 'completed'"];
        $params = [];

        // Tentukan rentang tanggal aktual
        // Prioritaskan startDate dan endDate jika diberikan
        if ($startDate && $endDate) {
            $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
            $response->actualStartDate = $startDate;
            $response->actualEndDate = $endDate;
        } else {
            // Jika startDate dan endDate tidak ada, gunakan $period sebagai default
            switch ($period) {
                case 'daily':
                    $defaultStartDate = date('Y-m-d', strtotime('-29 days')); // 30 hari termasuk hari ini
                    $defaultEndDate = date('Y-m-d');
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
                case 'yearly':
                    $currentYear = date('Y');
                    $defaultStartDate = $currentYear . '-01-01';
                    $defaultEndDate = $currentYear . '-12-31';
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
                case 'monthly':
                default:
                    $defaultStartDate = date('Y-m-01', strtotime('-11 months')); // 12 bulan termasuk bulan ini
                    $defaultEndDate = date('Y-m-t'); // Akhir bulan ini
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
            }
        }

        $whereSql = implode(" AND ", $whereClauses);

        // 1. Data untuk Grafik (Chart Data)
        $groupBy = "";
        $selectDate = "";
        $orderBy = "";

        $dateDiff = null;
        if($response->actualStartDate && $response->actualEndDate) {
            $startDt = new DateTime($response->actualStartDate);
            $endDt = new DateTime($response->actualEndDate);
            $dateDiff = $startDt->diff($endDt)->days;
        }
        
        // Tentukan pengelompokan grafik berdasarkan periode atau rentang tanggal
        if ($period === 'yearly' || ($dateDiff !== null && $dateDiff > 365 * 1.5)) {
            $selectDate = "YEAR(p.payment_date) as label";
            $groupBy = "YEAR(p.payment_date)";
            $orderBy = "label ASC";
            $response->periodLabel = 'Tahunan';
        } elseif ($period === 'monthly' || ($dateDiff !== null && $dateDiff > 60)) { // Group per bulan jika > 60 hari
            $selectDate = "DATE_FORMAT(p.payment_date, '%Y-%m') as label"; // Format YYYY-MM
            $groupBy = "label";
            $orderBy = "label ASC";
            $response->periodLabel = 'Bulanan';
        } else { // Group per hari jika <= 60 hari
            $selectDate = "DATE(p.payment_date) as label";
            $groupBy = "label";
            $orderBy = "label ASC";
            $response->periodLabel = 'Harian';
        }

        $chartQuery = "
            SELECT 
                {$selectDate},
                SUM(p.amount) as revenue
            FROM payments p
            WHERE {$whereSql}
            GROUP BY {$groupBy}
            ORDER BY {$orderBy}
        ";
        $this->db->query($chartQuery);
        foreach($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $response->chartData = $this->db->resultSet();

        // 2. Breakdown Pendapatan per Kategori Kamar
        $breakdownQuery = "
            SELECT 
                rc.name as category_name,
                SUM(p.amount) as revenue
            FROM payments p
            JOIN bookings b ON p.booking_id = b.booking_id
            JOIN rooms r ON b.room_id = r.room_id
            JOIN room_categories rc ON r.category_id = rc.category_id
            WHERE {$whereSql}
            GROUP BY rc.category_id, rc.name
            ORDER BY revenue DESC
        ";
        $this->db->query($breakdownQuery);
        foreach($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $response->breakdownData = $this->db->resultSet();

        // 3. Total Pendapatan Periode Ini
        $currentPeriodRevenueQuery = "
            SELECT SUM(p.amount) as total_revenue
            FROM payments p
            WHERE {$whereSql}
        ";
        $this->db->query($currentPeriodRevenueQuery);
        foreach($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $currentResult = $this->db->single();
        $response->currentPeriodRevenue = $currentResult ? (float)$currentResult->total_revenue : 0;

        // 4. Total Pendapatan Periode Sebelumnya (Contoh: periode yang sama persis sebelumnya)
        if ($response->actualStartDate && $response->actualEndDate) {
            try {
                $startDt = new DateTimeImmutable($response->actualStartDate);
                $endDt = new DateTimeImmutable($response->actualEndDate);
                // Hitung durasi periode saat ini
                $intervalSpec = 'P' . ($startDt->diff($endDt)->days + 1) . 'D'; // +1 karena inklusif
                $interval = new DateInterval($intervalSpec);


                // Mundur satu hari dari tanggal mulai saat ini untuk mendapatkan tanggal akhir periode sebelumnya
                $prevEndDate = $startDt->sub(new DateInterval('P1D'));
                // Kurangi durasi interval dari tanggal akhir periode sebelumnya untuk mendapatkan tanggal mulai periode sebelumnya
                $prevStartDate = $prevEndDate->sub($interval)->add(new DateInterval('P1D')); // Sesuaikan agar durasi sama

                $prevWhereClauses = ["p.payment_status = 'completed'"];
                $prevParams = [];
                $prevWhereClauses[] = "p.payment_date >= :prevStartDate AND p.payment_date < DATE_ADD(:prevEndDate, INTERVAL 1 DAY)";
                $prevParams[':prevStartDate'] = $prevStartDate->format('Y-m-d');
                $prevParams[':prevEndDate'] = $prevEndDate->format('Y-m-d');
                
                $prevWhereSql = implode(" AND ", $prevWhereClauses);

                $previousPeriodRevenueQuery = "
                    SELECT SUM(p.amount) as total_revenue
                    FROM payments p
                    WHERE {$prevWhereSql}
                ";
                $this->db->query($previousPeriodRevenueQuery);
                foreach($prevParams as $key => $value) {
                    $this->db->bind($key, $value);
                }
                $previousResult = $this->db->single();
                $response->previousPeriodRevenue = $previousResult ? (float)$previousResult->total_revenue : 0;
            } catch (Exception $e) {
                // Tangani error jika ada masalah dengan tanggal
                $response->previousPeriodRevenue = 0;
                 error_log("Error calculating previous period revenue: " . $e->getMessage());
            }
        }

        return $response;
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