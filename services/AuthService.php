<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Log.php";

class AuthService
{
    private $userModel;
    private $logModel;

    public function __construct()
    {
        // Get PDO connection
        global $pdo;
        if (!isset($pdo)) {
            // Try to create database connection
            if (file_exists(__DIR__ . '/../config/database.php')) {
                require_once __DIR__ . '/../config/database.php';
                $database = Database::getInstance();
                $pdo = $database->getConnection();
            } else {
                throw new Exception('Database configuration not found');
            }
        }

        $this->userModel = new User($pdo);
        $this->logModel = new Log();
    }

    /**
     * Register a new user
     */
    public function registerUser($data)
    {
        // Validate input data
        $validation = $this->validateRegistrationData($data);
        if (!$validation['valid']) {
            return $validation;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            return [
                'valid' => false,
                'message' => 'Barua pepe tayari ipo. Tafadhali tumia barua pepe tofauti au ingia.',
                'field' => 'email'
            ];
        }

        // Handle expert role registration
        if ($data['role'] === 'expert') {
            return $this->handleExpertRegistration($data);
        }

        // Create regular user
        $userId = $this->userModel->createUser($data);

        if ($userId) {
            // Log the registration activity
            $this->logActivity('Mtumiaji mpya mwenye barua pepe ' . $data['email'] . ' amejisajili kwenye mfumo', $userId);

            // Get the created user to start session
            $user = $this->userModel->getUserById($userId);
            if ($user) {
                // Start session and log user in
                $this->startUserSession($user);

                return [
                    'valid' => true,
                    'message' => 'Usajili umefaulu! Karibu kwenye Panda Digital.',
                    'user_id' => $userId,
                    'redirect_url' => $this->getRoleBasedRedirect($user['role'])
                ];
            }
        }

        return [
            'valid' => false,
            'message' => 'Usajili umeshindwa. Tafadhali jaribu tena.',
            'field' => 'general'
        ];
    }

    /**
     * Handle expert role registration with authorization requirement
     */
    private function handleExpertRegistration($data)
    {
        // Set expert_authorization to 0 (pending) for experts
        $data['expert_authorization'] = '0';
        // Note: account_status removed until database field is added

        $userId = $this->userModel->createUser($data);

        if ($userId) {
            // Log the expert registration
            $this->logActivity('Ombi la kuwa mtaalam limetumwa na ' . $data['email'], $userId);

            return [
                'valid' => true,
                'message' => 'Ombi lako la kuwa mtaalam limetumwa kikamilifu! Subiri kuidhinishwa na wasimamizi.',
                'user_id' => $userId,
                'redirect_url' => '/expert/pending-authorization.php',
                'requires_authorization' => true
            ];
        }

        return [
            'valid' => false,
            'message' => 'Usajili wa mtaalam umeshindwa. Tafadhali jaribu tena.',
            'field' => 'general'
        ];
    }

    /**
     * Authenticate user login
     */
    public function loginUser($email, $password)
    {
        // Validate input
        if (empty($email) || empty($password)) {
            return [
                'valid' => false,
                'message' => 'Tafadhali toa barua pepe na nenosiri.',
                'field' => 'general'
            ];
        }

        // Authenticate user
        $user = $this->userModel->authenticateUser($email, $password);

        if ($user) {
            // Check if expert is authorized
            if ($user['role'] === 'expert' && isset($user['expert_authorization']) && $user['expert_authorization'] == '0') {
                return [
                    'valid' => false,
                    'message' => 'Ombi lako la kuwa mtaalam bado halijaudhinishwa. Tafadhali subiri au wasiliana na wasimamizi.',
                    'field' => 'general',
                    'requires_authorization' => true
                ];
            }

            // Note: account_status check removed until database field is added
            // Check if user is active
            if (isset($user['is_active']) && $user['is_active'] != 1) {
                return [
                    'valid' => false,
                    'message' => 'Akaunti yako imefungwa au haijaudhinishwa. Tafadhali wasiliana na wasimamizi.',
                    'field' => 'general'
                ];
            }

            // Start session
            $this->startUserSession($user);

            // Log the login activity
            $this->logActivity('Mtumiaji mwenye barua pepe ' . $email . ' aliingia kwenye mfumo', $user['id']);

            return [
                'valid' => true,
                'message' => 'Kuingia kumefaulu! Karibu tena.',
                'user' => $user,
                'redirect_url' => $this->getRoleBasedRedirect($user['role'])
            ];
        }

        return [
            'valid' => false,
            'message' => 'Barua pepe au nenosiri batili. Tafadhali jaribu tena.',
            'field' => 'general'
        ];
    }

    /**
     * Logout user
     */
    public function logoutUser()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Log logout activity if user was logged in
            if (isset($_SESSION['user_id'])) {
                $this->logActivity('Mtumiaji mwenye ID ' . $_SESSION['user_id'] . ' alitoka kwenye mfumo', $_SESSION['user_id']);
            }

            // Destroy session
            session_destroy();
        }

        // Clear any cookies
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        return [
            'valid' => true,
            'message' => 'Umetoka kwenye mfumo.'
        ];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if session has expired
        if (isset($_SESSION['expires']) && $_SESSION['expires'] < time()) {
            $this->logoutUser();
            return false;
        }

        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current logged in user
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            // Get fresh user data from database to ensure we have all fields
            $userId = $_SESSION['user_id'];
            $userData = $this->userModel->getUserById($userId);

            if ($userData) {
                return $userData;
            }
        } catch (Exception $e) {
            error_log("Error fetching user data from database: " . $e->getMessage());
        }

        // Fallback to session data if database query fails
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name'],
            'phone' => $_SESSION['user_phone'] ?? '',
            'region' => $_SESSION['user_region'] ?? '',
            'business' => $_SESSION['user_business'] ?? '',
            'login_time' => $_SESSION['login_time'] ?? '',
            'expert_authorization' => $_SESSION['expert_authorization'] ?? null
        ];
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $_SESSION['user_id'];
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }

    /**
     * Require authentication - redirect if not logged in
     */
    public function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . app_url('login.php'));
            exit();
        }
    }

    /**
     * Require specific role - redirect if not authorized
     */
    public function requireRole($role)
    {
        $this->requireAuth();

        if (!$this->hasRole($role)) {
            header('Location: ' . app_url('unauthorized.php'));
            exit();
        }
    }

    /**
     * Start user session
     */
    private function startUserSession($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_phone'] = $user['phone'] ?? '';
        $_SESSION['user_region'] = $user['region'] ?? '';
        $_SESSION['user_business'] = $user['business'] ?? '';
        $_SESSION['expert_authorization'] = $user['expert_authorization'] ?? null;
        $_SESSION['login_time'] = time();

        // Set session timeout (24 hours)
        $_SESSION['expires'] = time() + (24 * 60 * 60);
    }

    /**
     * Get role-based redirect URL
     */
    public function getRoleBasedRedirect($role)
    {
        // Use the app_url() helper function to get the correct base URL
        $basePath = app_url('');
        switch ($role) {
            case 'admin':
                return $basePath . 'admin/dashboard.php';
            case 'expert':
                return $basePath . 'expert/dashboard.php';
            case 'user':
            default:
                return $basePath . 'user/dashboard.php';
        }
    }

    /**
     * Log user activity
     */
    private function logActivity($activity, $userId = null)
    {
        try {
            if ($this->logModel) {
                $this->logModel->logActivity($activity, $userId);
            }
        } catch (Exception $e) {
            // Silently fail logging to not break the main flow
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }

    /**
     * Validate registration data
     */
    private function validateRegistrationData($data)
    {
        $errors = [];

        // Required fields
        if (empty($data['first_name'])) {
            $errors[] = 'Jina la kwanza linahitajika.';
        }

        if (empty($data['last_name'])) {
            $errors[] = 'Jina la mwisho linahitajika.';
        }

        if (empty($data['email'])) {
            $errors[] = 'Barua pepe inahitajika.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Tafadhali toa barua pepe sahihi.';
        }

        if (empty($data['phone'])) {
            $errors[] = 'Nambari ya simu inahitajika.';
        }

        if (empty($data['password'])) {
            $errors[] = 'Nenosiri linahitajika.';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Nenosiri lazima liwe na herufi 6 au zaidi.';
        }

        if (empty($data['confirm_password'])) {
            $errors[] = 'Tafadhali thibitisha nenosiri lako.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Nenosiri hazifanani.';
        }

        if (empty($data['region'])) {
            $errors[] = 'Mkoa unahitajika.';
        }

        if (empty($data['business'])) {
            $errors[] = 'Kategoria ya biashara inahitajika.';
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => implode(' ', $errors),
                'field' => 'general'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Refresh session if needed
     */
    public function refreshSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if session has expired
        if (isset($_SESSION['expires']) && $_SESSION['expires'] < time()) {
            $this->logoutUser();
            return false;
        }

        // Extend session
        if (isset($_SESSION['user_id'])) {
            $_SESSION['expires'] = time() + (24 * 60 * 60);
            return true;
        }

        return false;
    }

    /**
     * Check if user can become expert
     */
    public function canBecomeExpert($userId)
    {
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            return false;
        }

        // Check if user is already an expert
        if ($user['role'] === 'expert') {
            return false;
        }

        // Check if user has pending expert request
        if (isset($user['expert_authorization']) && $user['expert_authorization'] == 0) {
            return false;
        }

        return true;
    }

    /**
     * Request expert role upgrade
     */
    public function requestExpertRole($userId, $bio = '')
    {
        if (!$this->canBecomeExpert($userId)) {
            return [
                'valid' => false,
                'message' => 'Huwezi kuomba kuwa mtaalam kwa sasa.'
            ];
        }

        $result = $this->userModel->requestExpertRole($userId, $bio);

        if ($result) {
            $this->logActivity('Ombi la kuwa mtaalam limetumwa', $userId);
            return [
                'valid' => true,
                'message' => 'Ombi lako la kuwa mtaalam limetumwa kikamilifu! Subiri kuidhinishwa.'
            ];
        }

        return [
            'valid' => false,
            'message' => 'Ombi lako limeshindwa. Tafadhali jaribu tena.'
        ];
    }
}
