<?php

/**
 * Payment Service using the exact working functions from the old code
 * This ensures compatibility with the proven AzamPay integration
 */
class PaymentService
{
    private $config;
    private $token;
    private $tokenExpiry;

    public function __construct()
    {
        // Load configuration
        $this->config = [
            'AZAMPAY_CLIENT_ID' => '85e67f8b-d8f1-4027-b819-5a11979ec1f0',
            'AZAMPAY_CLIENT_SECRET' => 'Ifsfj5rIDtj3ypEzJfxM6XY4CuifdDK0MNj7CPEahATLEBQbvfYBZhMq/1vEcBnxwLHKt5nXfUSXSEX8nRNm1bwyZTRKDQw9et+pEYh9WpTuVP5cmSMcOs/jlQj9RqO6hzJcw9hRwzIJIzfEp3VWbZJCdho8ja0WUb1VJnoHyHPFiC2eS1i+d2PGgUpdI6P1HP8SgmcKTDIYj4r37ilK3Nx9P/1a/sTEYXgISZhdDQUv7epDBOBqPCaSeJmn8qw2WA4hdKbvlvIf3LP50t5lencCSCSoe6Qj91myg2hqQGe6QPo2ZiIs56FCXcPmeP1UN3xGYlvjI2A/axYkafxzfDuplxeqx4ITdi9z55R/BVvhmTFRbcTdTMEeUfYXCaTbpjIu3yNsg6abBF+GnU6lQeVqK3i4FwY+TdmeS+QnB32d0Pm1ZKeg1ToxFM3RRwWdzSC6jkUT6aU+R3c+oavnH/mHTxdTxlTNkyirWdKcEwUYz8E4zgW76W9iSWbXfA9gOjb+SviW4LXqrd+jyWbfTZZdTnCQqGvtLgbwmxNEX301kqS2XCm7uLYUux+qoy+OGMzaD0Gir30SzT1lE8Bhfz/pjdPdwqCAYQgffD5UPOdi5Nhvd1hpf1Lk7IuORbQfVE8XuoH9QhgWT6CDAnbmYCC+uTXfVxixF7j9QobA=',
            'AZAMPAY_APP_NAME' => 'Panda Innovation'
        ];
    }

    /**
     * Generate a new AzamPay authentication token
     */
    private function generateToken()
    {
        $authUrl = 'https://authenticator.azampay.co.tz/AppRegistration/GenerateToken';

        $authData = [
            'clientId' => $this->config['AZAMPAY_CLIENT_ID'],
            'clientSecret' => $this->config['AZAMPAY_CLIENT_SECRET'],
            'appName' => $this->config['AZAMPAY_APP_NAME']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($authData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Failed to connect to AzamPay authentication service');
        }

        $responseData = json_decode($response, true);

        if (!isset($responseData['data']['accessToken'])) {
            throw new Exception('Invalid token response from AzamPay: ' . $response);
        }

        return $responseData['data']['accessToken'];
    }

    /**
     * Process payment request to AzamPay
     */
    private function processPaymentRequest($paymentData, $token, $clientId)
    {
        $checkoutUrl = "https://checkout.azampay.co.tz/azampay/mno/checkout";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $token,
            "X-API-Key: $clientId"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $checkoutUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Failed to connect to AzamPay checkout service');
        }

        return $response;
    }

    /**
     * Main payment processing method
     */
    public function processPayment($paymentData)
    {
        try {
            // Generate or get token
            $token = $this->generateToken();

            // Process payment request
            $response = $this->processPaymentRequest($paymentData, $token, $this->config['AZAMPAY_CLIENT_ID']);

            return $response;
        } catch (Exception $e) {
            throw new Exception('Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process course payment with proper data structure
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
