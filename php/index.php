<?php

require_once 'app/start.php';

if (isset($_POST) && !empty($_POST)) {

    if (isset($_POST['username']) && !empty($_POST['username']) && is_string($_POST['username']) && !empty(normal_text($_POST['username']))) {
        $username = normal_text($_POST['username']);
    } else {
        $errors[] = "Username cannot be empty";
    }
    
    if (isset($_POST['password']) && !empty($_POST['password']) && is_string($_POST['password']) && !empty(normal_text($_POST['password']))) {
        $password = normal_text($_POST['password']);
    } else {
        $errors[] = "Password cannot be empty";
    }

    if (empty($errors)) {

        $r = $a->verify_login ($username, $password);
        if ($r['status']) {
            $_SESSION['logged'] = $r['data']['user_id'];
            go(URL.'/admin');
        } else {
            $errors[] = "Invalid username or password";
        }

    }

}

include_once 'views/layout/header.view.php';
include_once 'views/login.view.php';
include_once 'views/layout/footer.view.php';
