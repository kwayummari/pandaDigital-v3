<?php
require_once __DIR__ . "/../services/AuthService.php";

class AuthMiddleware
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Require authentication for a page
     */
    public function requireAuth()
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /login.php');
            exit();
        }

        // Refresh session
        $this->authService->refreshSession();
    }

    /**
     * Require specific role for a page
     */
    public function requireRole($role)
    {
        $this->requireAuth();

        if (!$this->authService->hasRole($role)) {
            header('Location: /unauthorized.php');
            exit();
        }

        // Special handling for expert role
        if ($role === 'expert') {
            $this->requireExpertAuthorization();
        }
    }

    /**
     * Require admin role
     */
    public function requireAdmin()
    {
        $this->requireRole('admin');
    }

    /**
     * Require expert role with authorization
     */
    public function requireExpert()
    {
        $this->requireRole('expert');
    }

    /**
     * Require expert authorization (expert must be approved)
     */
    public function requireExpertAuthorization()
    {
        $currentUser = $this->authService->getCurrentUser();

        if (
            $currentUser['role'] === 'expert' &&
            isset($currentUser['expert_authorization']) &&
            $currentUser['expert_authorization'] == '0'
        ) {

            // Redirect to pending authorization page
            header('Location: /expert/pending-authorization.php');
            exit();
        }
    }

    /**
     * Check if user is logged in (for conditional display)
     */
    public function isLoggedIn()
    {
        return $this->authService->isLoggedIn();
    }

    /**
     * Get current user (for conditional display)
     */
    public function getCurrentUser()
    {
        return $this->authService->getCurrentUser();
    }

    /**
     * Get current user ID (for conditional display)
     */
    public function getCurrentUserId()
    {
        return $this->authService->getCurrentUserId();
    }

    /**
     * Check if user has specific role (for conditional display)
     */
    public function hasRole($role)
    {
        return $this->authService->hasRole($role);
    }

    /**
     * Check if user has expert authorization
     */
    public function hasExpertAuthorization()
    {
        $currentUser = $this->getCurrentUser();
        return $currentUser &&
            $currentUser['role'] === 'expert' &&
            isset($currentUser['expert_authorization']) &&
            $currentUser['expert_authorization'] == '1';
    }

    /**
     * Redirect if already logged in (for login/register pages)
     */
    public function redirectIfLoggedIn($redirectUrl = null)
    {
        if ($this->authService->isLoggedIn()) {
            if ($redirectUrl === null) {
                $currentUser = $this->authService->getCurrentUser();
                $redirectUrl = $this->authService->getRoleBasedRedirect($currentUser['role']);
            }
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    /**
     * Get role-based redirect URL
     */
    public function getRoleBasedRedirect($role)
    {
        return $this->authService->getRoleBasedRedirect($role);
    }

    /**
     * Check if user can access expert features
     */
    public function canAccessExpertFeatures()
    {
        $currentUser = $this->getCurrentUser();

        if (!$currentUser) {
            return false;
        }

        // Admin can access everything
        if ($currentUser['role'] === 'admin') {
            return true;
        }

        // Expert must be authorized
        if ($currentUser['role'] === 'expert') {
            return isset($currentUser['expert_authorization']) &&
                $currentUser['expert_authorization'] == '1';
        }

        return false;
    }

    /**
     * Check if user can become expert
     */
    public function canBecomeExpert()
    {
        return $this->authService->canBecomeExpert($this->getCurrentUserId());
    }
}
