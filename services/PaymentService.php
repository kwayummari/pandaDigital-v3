<?php

/**
 * Payment Service - EXACT COPY from working old code
 * This ensures 100% compatibility with the proven AzamPay integration
 */
class PaymentService
{
    private $config;

    public function __construct()
    {
        // Load configuration - EXACT COPY from old working code
        $this->config = [
            'AZAMPAY_CLIENT_ID' => '85e67f8b-d8f1-4027-b819-5a11979ec1f0',
            'AZAMPAY_CLIENT_SECRET' => 'Ifsfj5rIDtj3ypEzJfxM6XY4CuifdDK0MNj7CPEahATLEBQbvfYBZhMq/1vEcBnxwLHKt5nXfUSXSEX8nRNm1bwyZTRKDQw9et+pEYh9WpTuVP5cmSMcOs/jlQj9RqO6hzJcw9hRwzIJIzfEp3VWbZJCdho8ja0WUb1VJnoHyHPFiC2eS1i+d2PGgUpdI6P1HP8SgmcKTDIYj4r37ilK3Nx9P/1a/sTEYXgISZhdDQUv7epDBOBqPCaSeJmn8qw2WA4hdKbvlvIf3LP50t5lencCSCSoe6Qj91myg2hqQGe6QPo2ZiIs56FCXcPmeP1UN3xGYlvjI2A/axYkafxzfDuplxeqx4ITdi9z55R/BVvhmTFRbcTdTMEeUfYXCaTbpjIu3yNsg6abBF+GnU6lQeVqK3i4FwY+TdmeS+QnB32d0Pm1ZKeg1ToxFM3RRwWdzSC6jkUT6aU+R3c+oavnH/mHTxdTxlTNkyirWdKcEwUYz8E4zgW76W9iSWbXfA9gOjb+SviW4LXqrd+jyWbfTZZdTnCQqGvtLgbwmxNEX301kqS2XCm7uLYUux+qoy+OGMzaD0Gir30SzT1lE8Bhfz/pjdPdwqCAYQgffD5UPOdi5Nhvd1hpf1Lk7IuORbQfVE8XuoH9QhgWT6CDAnbmYCC+uTXfVxixF7j9QobA='
        ];
    }

    /**
     * Generate new AzamPay authentication token - EXACT COPY from working old code
     */
    private function generateNewToken($config)
    {
        $authUrl = 'https://authenticator.azampay.co.tz/AppRegistration/GenerateToken';

        $authData = [
            'clientId' => $config['AZAMPAY_CLIENT_ID'],
            'clientSecret' => $config['AZAMPAY_CLIENT_SECRET'],
            'appName' => 'Panda Innovation'
        ];

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
            throw new Exception('Error fetching token from AzamPay');
        }

        $authResponseData = json_decode($authResponse, true);
        if (!isset($authResponseData['data']['accessToken'])) {
            error_log("Invalid token response: " . $authResponse);
            throw new Exception('Error: Invalid token response from AzamPay');
        }

        return $authResponseData['data']['accessToken'];
    }

    /**
     * Process payment through AzamPay - EXACT COPY from working old code
     */
    private function processPaymentRequest($paymentData, $token, $clientId)
    {
        $checkoutUrl = "https://checkout.azampay.co.tz/azampay/mno/checkout";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $token,
            "X-API-Key: $clientId"
        ];

        // Log the request for debugging
        error_log("AzamPay Request: " . json_encode($paymentData));

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
            throw new Exception("Unable to process payment request - network error");
        }

        // Log the response for debugging
        error_log("AzamPay Response: " . $response);

        return $response;
    }

    /**
     * Process payment using the exact same logic as the working old code
     */
    public function processPayment($paymentData)
    {
        try {
            // Get token using the exact same method as old code
            $token = $this->generateNewToken($this->config);
            error_log("Token generated successfully: " . substr($token, 0, 20) . "...");

            // Process payment using the exact same method as old code
            $response = $this->processPaymentRequest($paymentData, $token, $this->config['AZAMPAY_CLIENT_ID']);

            $responseData = json_decode($response, true);

            if (!isset($responseData['transactionId'])) {
                // Log the full response for debugging
                error_log("AzamPay Error Response: " . $response);

                $errorMessage = "Transaction ID not found in response";
                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['statusMessage'])) {
                    $errorMessage = $responseData['statusMessage'];
                }

                throw new Exception("AzamPay Error: " . $errorMessage);
            }

            error_log("Payment processed successfully. Transaction ID: " . $responseData['transactionId']);

            return [
                'success' => true,
                'transactionId' => $responseData['transactionId'],
                'message' => 'Payment initiated successfully'
            ];
        } catch (Exception $e) {
            error_log("AzamPay Payment Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Process course payment with proper data structure - EXACT COPY from old working code
     */
    public function processCoursePayment($phone, $amount, $provider)
    {
        // Map provider names to AzamPay format
        $providerMap = [
            'mpesa' => 'mpesa',
            'tigopesa' => 'tigopesa',
            'airtel' => 'airtel',
            'halopesa' => 'halopesa'
        ];

        $mappedProvider = $providerMap[$provider] ?? $provider;

        // Prepare payment data - use simple externalId like old working code
        $paymentData = [
            'accountNumber' => $phone,
            'amount' => $amount,
            'currency' => 'TZS',
            'externalId' => '123', // Use simple ID like old working code
            'provider' => $mappedProvider,
            'additionalProperties' => null
        ];

        return $this->processPayment($paymentData);
    }
}
