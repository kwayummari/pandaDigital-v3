<?php

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/Environment.php';
require_once __DIR__ . '/../models/Course.php';

/**
 * Secure Payment Service for AzamPay Integration
 * Uses environment variables for API keys and secrets
 */
class PaymentService
{
    private $config;
    private $courseModel;
    private $token;
    private $tokenExpiry;

    public function __construct()
    {
        $this->config = Environment::getPaymentConfig();
        $this->courseModel = new Course();

        // Validate configuration
        if (empty($this->config['azampay_api_key']) || empty($this->config['azampay_secret_key'])) {
            throw new Exception('AzamPay configuration not found. Please check your .env file.');
        }
    }

    /**
     * Get or generate AzamPay authentication token
     */
    private function getAuthToken()
    {
        // Check if we have a valid cached token
        if ($this->token && $this->tokenExpiry && time() < $this->tokenExpiry) {
            $this->logToFile("Using cached token (expires in " . ($this->tokenExpiry - time()) . " seconds)");
            return $this->token;
        }

        $this->logToFile("Generating new AzamPay authentication token...");

        // Generate new token
        $authUrl = 'https://authenticator.azampay.co.tz/AppRegistration/GenerateToken';

        $authData = [
            'clientId' => $this->config['azampay_api_key'],
            'clientSecret' => $this->config['azampay_secret_key'],
            'appName' => 'Panda Innovation'
        ];

        $this->logToFile("Auth URL: " . $authUrl);
        $this->logToFile("Auth Data: " . json_encode($authData, JSON_PRETTY_PRINT));

        $authOptions = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($authData),
                'ignore_errors' => true
            ]
        ];

        $authContext = stream_context_create($authOptions);
        $authResponse = file_get_contents($authUrl, false, $authContext);

        if ($authResponse === false) {
            $this->logToFile("ERROR: Failed to fetch token from AzamPay - network error");
            throw new Exception('Error fetching token from AzamPay');
        }

        $this->logToFile("Raw AzamPay Response: " . $authResponse);

        $authResponseData = json_decode($authResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logToFile("ERROR: Invalid JSON response from AzamPay: " . json_last_error_msg());
            throw new Exception('Error: Invalid JSON response from AzamPay');
        }

        $this->logToFile("Parsed Response: " . json_encode($authResponseData, JSON_PRETTY_PRINT));

        if (!isset($authResponseData['data']['accessToken'])) {
            $this->logToFile("ERROR: Missing accessToken in response. Response structure: " . json_encode(array_keys($authResponseData)));
            if (isset($authResponseData['data'])) {
                $this->logToFile("ERROR: Data keys available: " . json_encode(array_keys($authResponseData['data'])));
            }
            throw new Exception('Error: Invalid token response from AzamPay');
        }

        // Cache token for 1 hour
        $this->token = $authResponseData['data']['accessToken'];
        $this->tokenExpiry = time() + 3600;

        $this->logToFile("SUCCESS: Token generated successfully. Token: " . substr($this->token, 0, 20) . "... (expires in 1 hour)");

        return $this->token;
    }

    /**
     * Log messages to local file for debugging
     */
    private function logToFile($message)
    {
        $logFile = __DIR__ . '/../logs/azampay_debug.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";

        // Create logs directory if it doesn't exist
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Process payment through AzamPay
     */
    public function processPayment($paymentData)
    {
        try {
            $this->logToFile("Starting payment process...");
            $this->logToFile("Payment Data: " . json_encode($paymentData, JSON_PRETTY_PRINT));

            $token = $this->getAuthToken();
            $this->logToFile("Token obtained successfully");

            $checkoutUrl = "https://checkout.azampay.co.tz/azampay/mno/checkout";

            // Map provider names to AzamPay format
            $providerMapping = [
                'mpesa' => 'mpesa',
                'tigo' => 'tigopesa', // Mix by YAS (tigo) maps to tigopesa for AzamPay
                'airtel' => 'airtel'
            ];

            $azamPayProvider = $providerMapping[$paymentData['provider']] ?? $paymentData['provider'];
            $paymentData['provider'] = $azamPayProvider;

            $this->logToFile("Provider mapped: {$paymentData['provider']} -> {$azamPayProvider}");

            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token,
                "X-API-Key: " . $this->config['azampay_api_key']
            ];

            // Log the request for debugging (without sensitive data)
            $logData = $paymentData;
            unset($logData['phone']); // Don't log phone numbers
            $this->logToFile("AzamPay Payment Request: " . json_encode($logData, JSON_PRETTY_PRINT));

            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => json_encode($paymentData),
                    'ignore_errors' => true
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents($checkoutUrl, false, $context);

            if ($response === false) {
                $this->logToFile("ERROR: Network error when making payment request");
                throw new Exception("Unable to process payment request - network error");
            }

            // Log the response for debugging
            $this->logToFile("AzamPay Response: " . $response);

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logToFile("ERROR: Invalid JSON response from AzamPay: " . json_last_error_msg());
                throw new Exception("AzamPay Error: Invalid JSON response");
            }

            $this->logToFile("Parsed Payment Response: " . json_encode($responseData, JSON_PRETTY_PRINT));

            if (!isset($responseData['transactionId'])) {
                $errorMessage = "Transaction ID not found in response";
                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['statusMessage'])) {
                    $errorMessage = $responseData['statusMessage'];
                }
                $this->logToFile("ERROR: " . $errorMessage);
                throw new Exception("AzamPay Error: " . $errorMessage);
            }

            $this->logToFile("SUCCESS: Payment processed successfully. Transaction ID: " . $responseData['transactionId']);

            return [
                'success' => true,
                'transactionId' => $responseData['transactionId'],
                'message' => 'Payment initiated successfully'
            ];
        } catch (Exception $e) {
            $this->logToFile("ERROR: AzamPay Payment Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create payment transaction record
     */
    public function createTransaction($userId, $courseId, $amount, $phone, $provider, $referenceNumber)
    {
        return $this->courseModel->createPaymentTransaction([
            'userId' => $userId,
            'courseId' => $courseId,
            'amount' => $amount,
            'phone' => $phone,
            'provider' => $provider,
            'referenceNumber' => $referenceNumber,
            'status' => 0 // pending
        ]);
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus($transactionId, $status)
    {
        return $this->courseModel->updatePaymentTransaction($transactionId, $status);
    }

    /**
     * Update transaction by reference number
     */
    public function updateTransactionByReference($referenceNumber, $status)
    {
        return $this->courseModel->updatePaymentTransactionByReference($referenceNumber, $status);
    }

    /**
     * Get pending transaction
     */
    public function getPendingTransaction($userId, $courseId)
    {
        return $this->courseModel->getPendingPaymentTransaction($userId, $courseId);
    }

    /**
     * Check if user has paid access
     */
    public function hasPaidAccess($userId, $courseId)
    {
        return $this->courseModel->hasPaidCourseAccess($userId, $courseId);
    }

    /**
     * Grant paid course access
     */
    public function grantCourseAccess($userId, $courseId)
    {
        return $this->courseModel->createPaidCourseAccess($userId, $courseId);
    }

    /**
     * Log payment callback
     */
    public function logCallback($callbackData)
    {
        return $this->courseModel->logPaymentCallback($callbackData);
    }
}
