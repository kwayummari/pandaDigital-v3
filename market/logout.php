<?php
session_start();
// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Return success response
http_response_code(200);
?>
