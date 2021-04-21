<?php

require_once '../app/start.php';

/* Check the authentication */
$auth = $a->check_auth();
if (!$auth['status']) {
    $_SESSION['message'] = ['type' => 'error', 'data' => 'You must be logged in to view page'];
    go(URL);
}
$auth = $auth['data'];
/* //--- Auth check end */

$errors = [];

$content_type = "";

$videos = $a->get_saved_videos($auth['user_id']);
if ($videos['status']) {
    $videos = $videos['data'];
} else {
    $videos = [];
}

$page_title = "Admin";
include_once DIR.'views/layout/header.view.php';
include_once DIR.'views/admin/saved.view.php';
include_once DIR.'views/layout/footer.view.php';

