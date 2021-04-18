<?php

    session_start();

    // Main project directory
    define('DIR', dirname(__DIR__).'/');
    
    // Either: development/production
    define('PROJECT_MODE', 'development'); 
    
    if (PROJECT_MODE !== 'development') {
        error_reporting(0);
    }

    // Database details
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'tiktok_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    // Timezone setting
    define('TIMEZONE', 'Asia/Karachi');
    date_default_timezone_set(TIMEZONE);

    // Auto load classes
    include DIR . 'app/auto_loader.php';

    // Functions
    include DIR . 'app/functions.php';

    // Get db handle
    $db = (new DB())->connect();
    $settings = new Settings($db);
    $a = new Account($db);

    define('URL', $settings->url());

    // checking for session message
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        if ($_SESSION['message']['type'] === 'success') {
            $s_success = $_SESSION['message']['data'];
        } else if ($_SESSION['message']['type'] === 'error') {
            $s_error = $_SESSION['message']['data'];
        }
        unset($_SESSION['message']);
    }
