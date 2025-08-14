<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../models/User.php";

class GoogleOAuthService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $userModel;

    public function __construct()
    {
        $config = Environment::getGoogleOAuthConfig();
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUri = $config['redirect_uri'];
        $this->userModel = new User();
    }

    /**
     * Get Google OAuth authorization URL
     */
    public function getAuthorizationUrl()
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'email profile',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code)
    {
        $tokenUrl = 'https://oauth2.googleapis.com/token';

        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        error_log("Google OAuth token error: HTTP $httpCode - $response");
        return false;
    }

    /**
     * Get user information from Google
     */
    public function getUserInfo($accessToken)
    {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        error_log("Google OAuth user info error: HTTP $httpCode - $response");
        return false;
    }

    /**
     * Handle Google OAuth callback and create/update user
     */
    public function handleCallback($code)
    {
        try {
            // Get access token
            $tokenData = $this->getAccessToken($code);
            if (!$tokenData || !isset($tokenData['access_token'])) {
                return [
                    'valid' => false,
                    'message' => 'Failed to get access token from Google'
                ];
            }

            // Get user information
            $userInfo = $this->getUserInfo($tokenData['access_token']);
            if (!$userInfo) {
                return [
                    'valid' => false,
                    'message' => 'Failed to get user information from Google'
                ];
            }

            // Check if user already exists
            $existingUser = $this->userModel->getUserByEmail($userInfo['email']);

            if ($existingUser) {
                // User exists - log them in
                return $this->loginExistingUser($existingUser);
            } else {
                // Create new user from Google data
                return $this->createUserFromGoogle($userInfo);
            }
        } catch (Exception $e) {
            error_log("Google OAuth callback error: " . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'An error occurred during Google authentication'
            ];
        }
    }

    /**
     * Login existing user
     */
    private function loginExistingUser($user)
    {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['expires'] = time() + (24 * 60 * 60); // 24 hours

        return [
            'valid' => true,
            'message' => 'Login successful',
            'user' => $user,
            'redirect_url' => $this->getRoleBasedRedirect($user['role'])
        ];
    }

    /**
     * Create new user from Google data
     */
    private function createUserFromGoogle($userInfo)
    {
        // Extract name parts
        $nameParts = explode(' ', $userInfo['name'], 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Prepare user data
        $userData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $userInfo['email'],
            'phone' => '', // Google doesn't provide phone
            'password' => $this->generateRandomPassword(), // Generate random password
            'role' => 'user', // Default role
            'region' => '', // User can update later
            'business' => '',
            'gender' => '',
            'date_of_birth' => '',
            'google_id' => $userInfo['id'] // Store Google ID for future reference
        ];

        // Create user
        $userId = $this->userModel->createUser($userData);

        if ($userId) {
            // Get the created user
            $user = $this->userModel->getUserById($userId);

            // Log them in
            return $this->loginExistingUser($user);
        }

        return [
            'valid' => false,
            'message' => 'Failed to create user account'
        ];
    }

    /**
     * Generate random password for Google users
     */
    private function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * Get role-based redirect URL
     */
    private function getRoleBasedRedirect($role)
    {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard.php';
            case 'expert':
                return '/expert/dashboard.php';
            case 'user':
            default:
                return '/user/dashboard.php';
        }
    }

    /**
     * Check if Google OAuth is configured
     */
    public function isConfigured()
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }
}
