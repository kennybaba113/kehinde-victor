<?php
// Start the session if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize history array if not exists
if (!isset($_SESSION['page_history'])) {
    $_SESSION['page_history'] = [];
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Add current page to history if it's not already the last one
if (empty($_SESSION['page_history']) || end($_SESSION['page_history']) != $current_page) {
    $_SESSION['page_history'][] = $current_page;
}

// Determine previous page
$prev_page = count($_SESSION['page_history']) >= 2 ? $_SESSION['page_history'][count($_SESSION['page_history'])-2] : 'dashboard.php';
?>
