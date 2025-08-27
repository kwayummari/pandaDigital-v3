<?php
require_once '../../config/init.php';
require_once '../../config/database.php';

/**
 * Payment Gateway Integration for Panda Market
 * Integrates with existing payment systems from pandadigitalV2
 */

class PaymentGateway
{
    private $conn;
    private $config;

    public function __construct($database)
    {
        $this->conn = $database->getConnection();
        $this->config = $this->loadConfig();
    }

    /**
     * Load payment configuration
     */
    private function loadConfig()
    {
        return [
            'mpesa' => [
                'phone' => '0767-680-463',
                'name' => 'M-Pesa'
            ],
            'airtel' => [
                'phone' => '0767-680-463',
                'name' => 'Airtel Money'
            ],
            'tigo' => [
                'phone' => '0767-680-463',
                'name' => 'Tigo Pesa'
            ],
            'halopesa' => [
                'phone' => '0767-680-463',
                'name' => 'HaloPesa'
            ]
        ];
    }

    /**
     * Process payment for cart items
     */
    public function processPayment($userId, $cartItems, $phone, $mobileType, $amount)
    {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            $referenceNo = $this->generateReferenceNo();
            $success = true;
            $processedItems = [];

            foreach ($cartItems as $item) {
                // Insert into sales table (existing structure)
                $insertQuery = "INSERT INTO sales (buyersId, productId, quantity, amount, phone, reference_no, mobile_type, status, date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, '0', NOW())";
                $insertStmt = $this->conn->prepare($insertQuery);

                $itemAmount = $item['price'] * $item['quantity'];

                $result = $insertStmt->execute([
                    $userId,
                    $item['product_id'],
                    $item['quantity'],
                    $itemAmount,
                    $phone,
                    $referenceNo,
                    $mobileType
                ]);

                if (!$result) {
                    throw new Exception("Failed to insert sales record for product {$item['product_id']}");
                }

                $processedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'amount' => $itemAmount,
                    'sales_id' => $this->conn->lastInsertId()
                ];
            }

            // Insert payment transaction record
            $paymentQuery = "INSERT INTO pandaTrans (msisdn, amount, message, utilityref, operator, reference, 
                                                   transactionstatus, transid, mnoreference, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW())";
            $paymentStmt = $this->conn->prepare($paymentQuery);

            $transId = 'TXN' . time() . rand(1000, 9999);
            $mnoRef = 'MNO' . time() . rand(1000, 9999);

            $paymentResult = $paymentStmt->execute([
                $phone,
                $amount,
                "Payment for order $referenceNo",
                $referenceNo,
                $mobileType,
                $referenceNo,
                $transId,
                $mnoRef
            ]);

            if (!$paymentResult) {
                throw new Exception("Failed to insert payment transaction record");
            }

            // Commit transaction
            $this->conn->commit();

            return [
                'success' => true,
                'reference_no' => $referenceNo,
                'trans_id' => $transId,
                'mno_ref' => $mnoRef,
                'processed_items' => $processedItems
            ];
        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->rollback();
            error_log("Payment processing error: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payment instructions for different mobile money services
     */
    public function getPaymentInstructions($mobileType, $amount, $referenceNo)
    {
        $config = $this->config[strtolower($mobileType)] ?? $this->config['mpesa'];

        $instructions = [
            'mpesa' => [
                'steps' => [
                    "1. Fungua M-Pesa menu",
                    "2. Chagua 'Send Money'",
                    "3. Weka namba: {$config['phone']}",
                    "4. Weka kiasi: TSh. " . number_format($amount, 0),
                    "5. Weka neno: $referenceNo",
                    "6. Thibitisha malipo"
                ],
                'phone' => $config['phone'],
                'note' => "Hakikisha unatumia neno '$referenceNo' kama reference"
            ],
            'airtel' => [
                'steps' => [
                    "1. Fungua Airtel Money menu",
                    "2. Chagua 'Send Money'",
                    "3. Weka namba: {$config['phone']}",
                    "4. Weka kiasi: TSh. " . number_format($amount, 0),
                    "5. Weka neno: $referenceNo",
                    "6. Thibitisha malipo"
                ],
                'phone' => $config['phone'],
                'note' => "Hakikisha unatumia neno '$referenceNo' kama reference"
            ],
            'tigo' => [
                'steps' => [
                    "1. Fungua Tigo Pesa menu",
                    "2. Chagua 'Send Money'",
                    "3. Weka namba: {$config['phone']}",
                    "4. Weka kiasi: TSh. " . number_format($amount, 0),
                    "5. Weka neno: $referenceNo",
                    "6. Thibitisha malipo"
                ],
                'phone' => $config['phone'],
                'note' => "Hakikisha unatumia neno '$referenceNo' kama reference"
            ],
            'halopesa' => [
                'steps' => [
                    "1. Fungua HaloPesa menu",
                    "2. Chagua 'Send Money'",
                    "3. Weka namba: {$config['phone']}",
                    "4. Weka kiasi: TSh. " . number_format($amount, 0),
                    "5. Weka neno: $referenceNo",
                    "6. Thibitisha malipo"
                ],
                'phone' => $config['phone'],
                'note' => "Hakikisha unatumia neno '$referenceNo' kama reference"
            ]
        ];

        return $instructions[strtolower($mobileType)] ?? $instructions['mpesa'];
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($referenceNo)
    {
        $query = "SELECT s.*, pt.transactionstatus, pt.transid, pt.mnoreference 
                  FROM sales s 
                  LEFT JOIN pandaTrans pt ON s.reference_no = pt.reference 
                  WHERE s.reference_no = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$referenceNo]);

        return $stmt->fetch();
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($referenceNo, $status, $transactionData = null)
    {
        try {
            // Update sales status
            $salesQuery = "UPDATE sales SET status = ? WHERE reference_no = ?";
            $salesStmt = $this->conn->prepare($salesQuery);
            $salesStmt->execute([$status == 'success' ? '1' : '0', $referenceNo]);

            // Update payment transaction status
            $paymentQuery = "UPDATE pandaTrans SET transactionstatus = ? WHERE reference = ?";
            $paymentStmt = $this->conn->prepare($paymentQuery);
            $paymentStmt->execute([$status, $referenceNo]);

            // Log status update
            error_log("Payment status updated for reference $referenceNo: $status");

            return true;
        } catch (Exception $e) {
            error_log("Error updating payment status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique reference number
     */
    private function generateReferenceNo()
    {
        return 'REF' . time() . rand(1000, 9999);
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods()
    {
        return [
            'mpesa' => [
                'name' => 'M-Pesa',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'success',
                'description' => 'Malipo kupitia M-Pesa'
            ],
            'airtel' => [
                'name' => 'Airtel Money',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'primary',
                'description' => 'Malipo kupitia Airtel Money'
            ],
            'tigo' => [
                'name' => 'Tigo Pesa',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'warning',
                'description' => 'Malipo kupitia Tigo Pesa'
            ],
            'halopesa' => [
                'name' => 'HaloPesa',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'info',
                'description' => 'Malipo kupitia HaloPesa'
            ]
        ];
    }
}
