<?php
// ============================================================
//  config.php  –  DB config + class loader
//
//  Include this ONE file at the top of any page that needs
//  database access. It loads all classes automatically.
// ============================================================

// ----------------------------------------------------------
// DATABASE CONFIGURATION
// Change host/dbname/username/password to match your setup
// ----------------------------------------------------------
// Check if running on localhost (local development)
$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || (strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0);

if ($isLocal) {
    $config = [
        'host'     => 'localhost',
        'dbname'   => 'students_db',
        'username' => 'root',
        'password' => '', // XAMPP default is empty, edit if needed
    ];
} else {
    // InfinityFree Live Server Database Configuration
    // Replace the password value below with your actual Control Panel password
    $config = [
        'host'     => 'sql101.infinityfree.com', 
        'dbname'   => 'if0_42030424_students_db',    
        'username' => 'if0_42034321',                
        'password' => 'W0hNTou5zsFv',      // <-- REPLACE THIS with your vPanel password (see guide below)
    ];
}

// ----------------------------------------------------------
// AUTOLOAD ALL CLASSES
// Loads every .php file inside the /classes folder
// ----------------------------------------------------------
foreach (glob(__DIR__ . '/classes/*.php') as $file) {
    require_once $file;
}

// ----------------------------------------------------------
// CREATE THE DATABASE CONNECTION (single shared instance)
// ----------------------------------------------------------
$database = new Database($config);
$conn     = $database->getConnection();