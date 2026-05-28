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
$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || (strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0);

if ($isLocal) {
    $config = [
        'host'     => 'localhost',
        'dbname'   => 'students_db',
        'username' => 'root',
        'password' => '', // XAMPP default is empty, edit if needed
    ];
} else {
    $config = [
        'host'     => 'sql207.infinityfree.com', 
        'dbname'   => 'if0_42034321_students_db',    
        'username' => 'if0_42034321',                
        'password' => 'W0hNTou5zsFv',      
    ];
}

foreach (glob(__DIR__ . '/classes/*.php') as $file) {
    require_once $file;
}

// ----------------------------------------------------------
// CREATE THE DATABASE CONNECTION (single shared instance)
// ----------------------------------------------------------
$database = new Database($config);
$conn     = $database->getConnection();