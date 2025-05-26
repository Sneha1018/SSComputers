<?php
// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to require login for specific pages
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

// Function to get current user's role
function getUserRole() {
    return $_SESSION['role'] ?? 'guest';
}

// Function to check if user is admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Function to require admin access
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// List of pages that don't require login
$public_pages = [
    'index.php',
    'login.php',
    'signup.php',
    'about.php',
    'contact.php',
    'products.php',
    'product.php',
    'search.php',
    'category.php'
];

// Function to check if current page requires login
function currentPageRequiresLogin() {
    global $public_pages;
    $current_page = basename($_SERVER['PHP_SELF']);
    return !in_array($current_page, $public_pages);
}
?> 