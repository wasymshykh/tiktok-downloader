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

$page = 1;
if (isset($_GET['p']) && !empty($_GET['p']) && is_numeric($_GET['p'])) {
    $page = $_GET['p'];
}

if (isset($_POST['hashtag']) && !empty($_POST['hashtag']) && is_string($_POST['hashtag']) && !empty(normal_text($_POST['hashtag']))) {
    // If form is submitted
    $hashtag = normal_text($_POST['hashtag']);
    go(URL."/admin/?h=$hashtag&p=$page");
}

if (isset($_GET['h']) && !empty($_GET['h']) && is_string($_GET['h']) && !empty(normal_text($_GET['h']))) {
    $hashtag = normal_text($_GET['h']);
}

$saved_ids = [];
$saved_videos = $a->get_saved_videos($auth['user_id']);
if ($saved_videos['status']) {
    $saved_videos = $saved_videos['data'];
    // mapping videos by id
    foreach ($saved_videos as $v) {
        $saved_ids[$v['video_id']] = $v;
    }
} else {
    $saved_videos = [];
}

$videos = [];
if (isset($hashtag) && !empty($hashtag)) {
    $videos = $a->search_hashtag($hashtag, $page);
    if ($videos['status']) {
        $videos = $videos['data'];
    } else {
        $errors[] = $videos['message'].": ".$videos['data'];
        $videos = [];
    }
}

$page_title = "Admin";
include_once DIR.'views/layout/header.view.php';
include_once DIR.'views/admin/index.view.php';
include_once DIR.'views/layout/footer.view.php';
