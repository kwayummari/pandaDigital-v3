<?php
require_once __DIR__ . "/../config/database.php";

class Sales
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllSalesForAdmin()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    s.id as sale_id,
                    s.reference_no,
                    s.amount,
                    s.quantity,
                    s.date as sale_date,
                    s.status as sale_status,
                    t.message as transaction_status,
                    p.name as product_name,
                    p.sellerId,
                    u.first_name,
                    u.last_name
                FROM sales s
                JOIN transactions t ON s.reference_no = t.reference
                JOIN products p ON s.productId = p.id
                JOIN users u ON p.sellerId = u.id
                ORDER BY s.date DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all sales for admin: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalSales()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "SELECT COUNT(*) as total FROM sales";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total sales: " . $e->getMessage());
            return 0;
        }
    }

    public function getSaleById($saleId)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    s.id as sale_id,
                    s.reference_no,
                    s.amount,
                    s.quantity,
                    s.date as sale_date,
                    s.status as sale_status,
                    t.message as transaction_status,
                    p.name as product_name,
                    p.sellerId,
                    u.first_name,
                    u.last_name
                FROM sales s
                JOIN transactions t ON s.reference_no = t.reference
                JOIN products p ON s.productId = p.id
                JOIN users u ON p.sellerId = u.id
                WHERE s.id = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$saleId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting sale by ID: " . $e->getMessage());
            return null;
        }
    }

    public function deleteSale($saleId)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "DELETE FROM sales WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$saleId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting sale: " . $e->getMessage());
            return false;
        }
    }

    public function updateSale($saleId, $saleData)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "UPDATE sales SET amount = ?, quantity = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $saleData['amount'],
                $saleData['quantity'],
                $saleData['status'],
                $saleId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating sale: " . $e->getMessage());
            return false;
        }
    }

    public function addSale($saleData)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "INSERT INTO sales (reference_no, productId, buyersId, amount, quantity, date) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $saleData['reference_no'],
                $saleData['productId'],
                $saleData['buyersId'],
                $saleData['amount'],
                $saleData['quantity'],
                $saleData['date']
            ]);

            return $conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error adding sale: " . $e->getMessage());
            return false;
        }
    }

    public function getOverallSalesStats()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    COUNT(*) as total_sales,
                    SUM(s.amount) as total_revenue,
                    SUM(s.amount * 0.06) as company_profit,
                    COUNT(DISTINCT p.sellerId) as total_sellers
                FROM sales s
                JOIN transactions t ON s.reference_no = t.reference
                JOIN products p ON s.productId = p.id
                WHERE t.message = 'Success'
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();

            return [
                'total_sales' => $result['total_sales'] ?? 0,
                'total_revenue' => $result['total_revenue'] ?? 0,
                'company_profit' => $result['company_profit'] ?? 0,
                'total_sellers' => $result['total_sellers'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting overall sales stats: " . $e->getMessage());
            return [
                'total_sales' => 0,
                'total_revenue' => 0,
                'company_profit' => 0,
                'total_sellers' => 0
            ];
        }
    }

    public function getMonthlySalesStats()
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    DATE_FORMAT(date_created, '%Y-%m') as month,
                    SUM(amount) as revenue,
                    COUNT(*) as transactions
                FROM transactions 
                WHERE status = 'completed' 
                AND date_created >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(date_created, '%Y-%m')
                ORDER BY month ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting monthly sales stats: " . $e->getMessage());
            return [];
        }
    }

    public function getTopSellingProducts($startDate = null, $endDate = null, $limit = 5)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    p.name as product_name,
                    COUNT(t.id) as total_sold,
                    SUM(t.amount) as total_revenue
                FROM transactions t
                LEFT JOIN products p ON t.product_id = p.id
                WHERE t.status = 'completed'
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(t.date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY p.id, p.name ORDER BY total_revenue DESC LIMIT ?";
            $params[] = $limit;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top selling products: " . $e->getMessage());
            return [];
        }
    }

    public function addTransaction($userId, $productId, $amount, $paymentMethod, $description = null)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Generate unique transaction ID
            $transactionId = 'TXN' . date('Ymd') . rand(1000, 9999);

            $stmt = $conn->prepare("
                INSERT INTO transactions (transaction_id, user_id, product_id, amount, payment_method, description, status, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            return $stmt->execute([$transactionId, $userId, $productId, $amount, $paymentMethod, $description]);
        } catch (PDOException $e) {
            error_log("Error adding transaction: " . $e->getMessage());
            return false;
        }
    }

    public function updateTransactionStatus($transactionId, $status)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if transaction exists
            $stmt = $conn->prepare("SELECT id FROM transactions WHERE id = ?");
            $stmt->execute([$transactionId]);
            if (!$stmt->fetch()) {
                return false;
            }

            $stmt = $conn->prepare("UPDATE transactions SET status = ?, date_updated = NOW() WHERE id = ?");
            return $stmt->execute([$status, $transactionId]);
        } catch (PDOException $e) {
            error_log("Error updating transaction status: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTransaction($transactionId)
    {
        try {
            $conn = $this->db->getConnection();

            // Check if transaction exists
            $stmt = $conn->prepare("SELECT id FROM transactions WHERE id = ?");
            $stmt->execute([$transactionId]);
            if (!$stmt->fetch()) {
                return false;
            }

            // Delete transaction (you might want to soft delete instead)
            $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
            return $stmt->execute([$transactionId]);
        } catch (PDOException $e) {
            error_log("Error deleting transaction: " . $e->getMessage());
            return false;
        }
    }

    public function getTransactionById($transactionId)
    {
        try {
            $conn = $this->db->getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    t.*, u.first_name, u.last_name, u.email,
                    p.name as product_name, p.description as product_description
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN products p ON t.product_id = p.id
                WHERE t.id = ?
            ");
            $stmt->execute([$transactionId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting transaction by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getUserTransactions($userId, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    t.*, p.name as product_name
                FROM transactions t
                LEFT JOIN products p ON t.product_id = p.id
                WHERE t.user_id = ?
                ORDER BY t.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user transactions: " . $e->getMessage());
            return [];
        }
    }

    public function getTransactionsByStatus($status, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    t.*, u.first_name, u.last_name, u.email,
                    p.name as product_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN products p ON t.product_id = p.id
                WHERE t.status = ?
                ORDER BY t.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$status, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting transactions by status: " . $e->getMessage());
            return [];
        }
    }

    public function getTransactionsByPaymentMethod($paymentMethod, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $stmt = $conn->prepare("
                SELECT 
                    t.*, u.first_name, u.last_name, u.email,
                    p.name as product_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN products p ON t.product_id = p.id
                WHERE t.payment_method = ?
                ORDER BY t.date_created DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$paymentMethod, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting transactions by payment method: " . $e->getMessage());
            return [];
        }
    }

    public function searchTransactions($searchTerm, $startDate = null, $endDate = null, $page = 1, $perPage = 10)
    {
        try {
            $conn = $this->db->getConnection();
            $offset = ($page - 1) * $perPage;

            $sql = "
                SELECT 
                    t.*, u.first_name, u.last_name, u.email,
                    p.name as product_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN products p ON t.product_id = p.id
                WHERE (t.transaction_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)
            ";
            $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(t.date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " ORDER BY t.date_created DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching transactions: " . $e->getMessage());
            return [];
        }
    }

    public function getSalesReport($startDate = null, $endDate = null, $groupBy = 'day')
    {
        try {
            $conn = $this->db->getConnection();

            $dateFormat = 'Y-%m-%d';
            if ($groupBy === 'month') {
                $dateFormat = 'Y-%m';
            } elseif ($groupBy === 'year') {
                $dateFormat = 'Y';
            }

            $sql = "
                SELECT 
                    DATE_FORMAT(date_created, ?) as period,
                    COUNT(*) as transactions,
                    SUM(amount) as revenue,
                    COUNT(DISTINCT user_id) as customers
                FROM transactions 
                WHERE status = 'completed'
            ";
            $params = [$dateFormat];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $sql .= " GROUP BY DATE_FORMAT(date_created, ?) ORDER BY period ASC";
            $params[] = $dateFormat;

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting sales report: " . $e->getMessage());
            return [];
        }
    }

    public function getRefundStats($startDate = null, $endDate = null)
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    COUNT(*) as total_refunds,
                    SUM(amount) as refunded_amount,
                    AVG(amount) as average_refund
                FROM transactions 
                WHERE status = 'refunded'
            ";
            $params = [];

            if ($startDate && $endDate) {
                $sql .= " AND DATE(date_created) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting refund stats: " . $e->getMessage());
            return [
                'total_refunds' => 0,
                'refunded_amount' => 0,
                'average_refund' => 0
            ];
        }
    }

    /**
     * Get financial metrics for admin dashboard
     */
    public function getFinancialMetrics()
    {
        try {
            $conn = $this->db->getConnection();

            $sql = "
                SELECT 
                    COUNT(*) as total_sales,
                    SUM(amount) as total_income,
                    SUM(amount * 0.06) as company_profit
                FROM transactions 
                WHERE status = 'completed'
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();

            return [
                'total_sales' => $result['total_sales'] ?? 0,
                'total_income' => $result['total_income'] ?? 0,
                'company_profit' => $result['company_profit'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting financial metrics: " . $e->getMessage());
            return [
                'total_sales' => 0,
                'total_income' => 0,
                'company_profit' => 0
            ];
        }
    }

    /**
     * Get recent transactions for admin dashboard
     */
    public function getAllTransactionsForAdmin($startDate = null, $endDate = null, $page = 1, $limit = 10)
    {
        try {
            $conn = $this->db->getConnection();

            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT 
                    t.id,
                    t.reference,
                    t.amount,
                    t.status,
                    t.date_created,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                ORDER BY t.date_created DESC
                LIMIT ? OFFSET ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting recent transactions for admin: " . $e->getMessage());
            return [];
        }
    }
}
