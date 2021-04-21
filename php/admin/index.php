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
    
    $form_post = $_POST['hashtag'];
    $hashtag = normal_text($_POST['hashtag']);
    go(URL."/admin/?h=$hashtag&p=1");

} else if (isset($_POST['profile']) && !empty($_POST['profile']) && is_string($_POST['profile']) && !empty(normal_text($_POST['profile']))) {

    $form_post = $_POST['profile'];
    $profile_name = normal_text($_POST['profile']);
    
    $p_match = [];
    preg_match_all('/\.com\/@(.+)/', $profile_name, $p_match);
    if (!empty($p_match[1])) {
        $profile_name = rtrim($p_match[1][0], '/');
    }

    go(URL."/admin/?u=$profile_name&p=1");

} else if (isset($_POST['video']) && !empty($_POST['video']) && is_string($_POST['video']) && !empty(normal_text($_POST['video']))) {
    
    $form_post = $_POST['video'];
    $video_link = normal_text($_POST['video']);
    $p_match = [];
    preg_match_all('/\/@(.+)\/video\/(\d+)/', $video_link, $p_match);

    if (empty($p_match[1]) || empty($p_match[2])) {
        $errors[] = "URL format is invalid. Example format is: <i>https://www.tiktok.com/@Jack/video/6787475722014297349</i>";
    } else {
        $profile_name = $p_match[1][0];
        $video_id = $p_match[2][0];
        
        if (!is_string($profile_name)) {
            $errors[] = "Profile name must be a string";
        }
        if (!is_numeric($video_id)) {
            $errors[] = "Video id must be numeric";
        }
        if (empty($errors)) {
            go(URL."/admin/?n=$profile_name&v=$video_id");
        }
    }

}

$content_type = "";

if (isset($_GET['h'])) {
    
    if (!empty($_GET['h']) && is_string($_GET['h']) && !empty(normal_text($_GET['h']))) {
        $hashtag = normal_text($_GET['h']);
    } else {
        go(URL.'/admin/');
    }
    $content_type = "hashtag";

} else if (isset($_GET['u'])) { 

    if (!empty($_GET['u']) && is_string($_GET['u']) && !empty(normal_text($_GET['u']))) {
        $profile_name = normal_text($_GET['u']);
    } else {
        go(URL.'/admin/');
    }
    $content_type = "profile";

} else if (isset($_GET['n']) && isset($_GET['v'])) {
    
    if (!empty($_GET['n']) && is_string($_GET['n']) && !empty(normal_text($_GET['n']))) {
        $profile_name = normal_text($_GET['n']);
    } else {
        go(URL.'/admin/');
    }
    if (!empty($_GET['v']) && is_numeric($_GET['v']) && !empty(normal_text($_GET['v']))) {
        $video_id = normal_text($_GET['v']);
    } else {
        go(URL.'/admin/');
    }
    $content_type = "video";

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
if ($content_type === 'hashtag') {

    $videos = $a->search_hashtag($hashtag, $page);
    if ($videos['status']) {
        $videos = $videos['data'];
    } else {
        $errors[] = $videos['message'].": ".$videos['data'];
        $videos = [];
    }

} else if ($content_type === 'profile') {
    
    $videos = $a->search_profile($profile_name);
    if ($videos['status']) {
        $videos = $videos['data'];
    } else {
        $errors[] = $videos['message'].": ".$videos['data'];
        $videos = [];
    }

} else if ($content_type === 'video') {
    
    $videos = $a->search_video($profile_name, $video_id);
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
