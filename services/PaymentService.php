<?php

/**
 * Payment Service - EXACT COPY from pandadigitalV2/market/process_order.php
 * Using the working functions exactly as they are without any changes
 */

// Load configuration - EXACT COPY
$config = [
    'AZAMPAY_CLIENT_ID' => '85e67f8b-d8f1-4027-b819-5a11979ec1f0',
    'AZAMPAY_CLIENT_SECRET' => 'Ifsfj5rIDtj3ypEzJfxM6XY4CuifdDK0MNj7CPEahATLEBQbvfYBZhMq/1vEcBnxwLHKt5nXfUSXSEX8nRNm1bwyZTRKDQw9et+pEYh9WpTuVP5cmSMcOs/jlQj9RqO6hzJcw9hRwzIJIzfEp3VWbZJCdho8ja0WUb1VJnoHyHPFiC2eS1i+d2PGgUpdI6P1HP8SgmcKTDIYj4r37ilK3Nx9P/1a/sTEYXgISZhdDQUv7epDBOBqPCaSeJmn8qw2WA4hdKbvlvIf3LP50t5lencCSCSoe6Qj91myg2hqQGe6QPo2ZiIs56FCXcPmeP1UN3xGYlvjI2A/axYkafxzfDuplxeqx4ITdi9z55R/BVvhmTFRbcTdTMEeUfYXCaTbpjIu3yNsg6abBF+GnU6lQeVqK3i4FwY+TdmeS+QnB32d0Pm1ZKeg1ToxFM3RRwWdzSC6jkUT6aU+R3c+oavnH/mHTxdTxlTNkyirWdKcEwUYz8E4zgW76W9iSWbXfA9gOjb+SviW4LXqrd+jyWbfTZZdTnCQqGvtLgbwmxNEX301kqS2XCm7uLYUux+qoy+OGMzaD0Gir30SzT1lE8Bhfz/pjdPdwqCAYQgffD5UPOdi5Nhvd1hpf1Lk7IuORbQfVE8XuoH9QhgWT6CDAnbmYCC+uTXfVxixF7j9QobA='
];

// EXACT COPY of working functions - NO CHANGES
function generateNewToken($config)
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
        throw new Exception('Error fetching token');
    }

    $authResponseData = json_decode($authResponse, true);
    if (!isset($authResponseData['data']['accessToken'])) {
        throw new Exception('Error: Invalid token response');
    }

    return $authResponseData['data']['accessToken'];
}

function processPayment($paymentData, $token, $clientId)
{
    $checkoutUrl = "https://checkout.azampay.co.tz/azampay/mno/checkout";

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token,
        "X-API-Key: $clientId"
    ];

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
        throw new Exception("Unable to process payment request");
    }

    return $response;
}

// Wrapper class that calls the working functions exactly as they are
class PaymentService
{
    public function processCoursePayment($phone, $amount, $provider)
    {
        try {
            // Call the working functions exactly as they are - NO CHANGES
            $clientId = $GLOBALS['config']['AZAMPAY_CLIENT_ID'];
            $storedToken = $_SESSION['token'] ?? null;
            $storedExpiry = $_SESSION['token_expiry'] ?? null;

            $currentTime = time();
            if (!$storedToken || !$storedExpiry || $currentTime >= strtotime($storedExpiry)) {
                $token = generateNewToken($GLOBALS['config']);
                $_SESSION['token'] = $token;
                $_SESSION['token_expiry'] = date('Y-m-d\TH:i:s\Z', strtotime('+1 hour'));
            } else {
                $token = $storedToken;
            }

            // Call processPayment exactly as it is - NO CHANGES
            $response = processPayment([
                'accountNumber' => $phone,
                'amount' => $amount,
                'currency' => 'TZS',
                'externalId' => '123',
                'provider' => $provider,
                'additionalProperties' => null
            ], $token, $clientId);

            $responseData = json_decode($response, true);

            if (!isset($responseData['transactionId'])) {
                throw new Exception("Error: Transaction ID not found in the response.");
            }

            return [
                'success' => true,
                'transactionId' => $responseData['transactionId'],
                'message' => 'Payment initiated successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
