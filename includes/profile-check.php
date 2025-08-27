<?php

/**
 * Profile Check Middleware
 * Include this file in pages where profile completion is required
 * 
 * Usage:
 * require_once __DIR__ . '/profile-check.php';
 * 
 * Then call: checkProfileCompletion('action_name', 'Action Display Name');
 */

// Initialize User model if not already done
if (!isset($userModel) && isset($pdo)) {
    try {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User($pdo);
    } catch (Exception $e) {
        error_log('Profile check error: ' . $e->getMessage());
        $userModel = null;
    }
}

// Include profile completion modal only if we have a user model
if ($userModel) {
    require_once __DIR__ . '/profile-completion-modal.php';
}

/**
 * Check if profile completion is required for a specific action
 * 
 * @param string $action The action being attempted
 * @param string $actionName Display name for the action
 * @return bool True if action can proceed, false if profile completion is required
 */
function checkProfileCompletion($action, $actionName = '')
{
    global $userModel;
    
    // Check if user is logged in
    if (!isset($_SESSION['userId'])) {
        // Redirect to login
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    // Check if user model is available
    if (!$userModel) {
        // User model not available, allow action to proceed
        error_log('User model not available in checkProfileCompletion');
        return true;
    }
    
    $userId = $_SESSION['userId'];
    
    try {
        // Check if user can perform the action
        if ($userModel->canPerformAction($userId, $action)) {
            return true; // Action can proceed
        }
        
        // Profile completion is required
        // Store the action in session for after completion
        $_SESSION['pending_action'] = $action;
        $_SESSION['pending_action_name'] = $actionName;
        $_SESSION['pending_action_url'] = $_SERVER['REQUEST_URI'];
        
        // Show profile completion modal
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof showProfileCompletionModal === 'function') {
                    showProfileCompletionModal('$action', '$actionName');
                } else {
                    console.log('Profile completion modal not available');
                }
            });
        </script>";
        
        return false; // Action cannot proceed
    } catch (Exception $e) {
        error_log('Error in checkProfileCompletion: ' . $e->getMessage());
        // Allow action to proceed if there's an error
        return true;
    }
}

/**
 * Check if profile completion is required and redirect if needed
 * 
 * @param string $action The action being attempted
 * @param string $actionName Display name for the action
 * @param string $redirectUrl URL to redirect to after profile completion
 */
function requireProfileCompletion($action, $actionName = '', $redirectUrl = '')
{
    global $userModel;
    
    // Check if user is logged in
    if (!isset($_SESSION['userId'])) {
        // Redirect to login
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    // Check if user model is available
    if (!$userModel) {
        // User model not available, allow action to proceed
        error_log('User model not available in requireProfileCompletion');
        return true;
    }
    
    try {
        $userId = $_SESSION['userId'];
        
        // Check if user can perform the action
        if ($userModel->canPerformAction($userId, $action)) {
            return true; // Action can proceed
        }
        
        // Store pending action details
        $_SESSION['pending_action'] = $action;
        $_SESSION['pending_action_name'] = $actionName;
        $_SESSION['pending_action_url'] = $redirectUrl ?: $_SERVER['REQUEST_URI'];
        
        // Redirect to profile completion page
        header('Location: /complete-profile.php?action=' . urlencode($action) . '&redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    } catch (Exception $e) {
        error_log('Error in requireProfileCompletion: ' . $e->getMessage());
        // Allow action to proceed if there's an error
        return true;
    }
}

/**
 * Get profile completion status for display
 * 
 * @return array Profile completion status
 */
function getProfileCompletionStatus()
{
    global $userModel;
    
    if (!isset($_SESSION['userId']) || !$userModel) {
        return null;
    }
    
    try {
        return $userModel->getProfileCompletionStatus($_SESSION['userId']);
    } catch (Exception $e) {
        error_log('Error in getProfileCompletionStatus: ' . $e->getMessage());
        return null;
    }
}

/**
 * Check if user has completed minimum profile requirements
 * 
 * @return bool True if minimum requirements are met
 */
function hasMinimumProfile()
{
    global $userModel;
    
    if (!isset($_SESSION['userId']) || !$userModel) {
        return false;
    }
    
    try {
        $userId = $_SESSION['userId'];
        
        // Minimum requirements: first_name, last_name, phone
        $minimumFields = ['first_name', 'last_name', 'phone'];
        
        foreach ($minimumFields as $field) {
            if (!$userModel->canPerformAction($userId, 'study_course')) {
                return false;
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log('Error in hasMinimumProfile: ' . $e->getMessage());
        return false;
    }
}

/**
 * Display profile completion progress bar
 * 
 * @param string $size Size class for the progress bar (sm, md, lg)
 */
function displayProfileProgress($size = 'md')
{
    $status = getProfileCompletionStatus();
    
    if (!$status) {
        return;
    }
    
    try {
        $sizeClass = $size === 'sm' ? 'progress-sm' : ($size === 'lg' ? 'progress-lg' : '');
        $height = $size === 'sm' ? '4px' : ($size === 'lg' ? '12px' : '8px');
        
        echo '<div class="profile-progress ' . $sizeClass . ' mb-3">';
        echo '<div class="d-flex justify-content-between align-items-center mb-2">';
        echo '<span class="fw-bold">Ukomo wa Wasifu:</span>';
        echo '<span class="badge bg-primary">' . $status['percentage'] . '%</span>';
        echo '</div>';
        echo '<div class="progress" style="height: ' . $height . ';">';
        echo '<div class="progress-bar bg-primary" role="progressbar" 
                    style="width: ' . $status['percentage'] . '%" 
                    aria-valuenow="' . $status['percentage'] . '" 
                    aria-valuemin="0" aria-valuemax="100"></div>';
        echo '</div>';
        echo '<small class="text-muted">';
        echo $status['completed'] . ' kati ya ' . $status['total'] . ' sehemu zimekamilika';
        echo '</small>';
        echo '</div>';
    } catch (Exception $e) {
        error_log('Error in displayProfileProgress: ' . $e->getMessage());
        return;
    }
}

/**
 * Display profile completion requirements for a specific action
 * 
 * @param string $action The action being attempted
 */
function displayActionRequirements($action)
{
    global $userModel;
    
    if (!isset($_SESSION['userId']) || !$userModel) {
        return;
    }
    
    try {
        $userId = $_SESSION['userId'];
        $missingFields = $userModel->getMissingFieldsForAction($userId, $action);
        
        if (empty($missingFields)) {
            return;
        }
        
        $fieldLabels = $userModel->getFieldLabels();
        
        echo '<div class="alert alert-warning">';
        echo '<h6><i class="fas fa-exclamation-triangle me-2"></i>Sehemu Zinazohitajika:</h6>';
        echo '<p>Unahitaji kukamilisha sehemu zifuatazo ili kuendelea:</p>';
        echo '<ul class="mb-0">';
        
        foreach ($missingFields as $field) {
            $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            echo '<li>' . htmlspecialchars($label) . '</li>';
        }
        
        echo '</ul>';
        echo '<button type="button" class="btn btn-primary btn-sm mt-2" onclick="showProfileCompletionModal(\'' . $action . '\', \'\')">';
        echo '<i class="fas fa-user-edit me-2"></i>Kamilisha Wasifu';
        echo '</button>';
        echo '</div>';
    } catch (Exception $e) {
        error_log('Error in displayActionRequirements: ' . $e->getMessage());
        return;
    }
}
