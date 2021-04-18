<?php

require_once '../app/start.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$errors = [];

if (isset($_POST['save_video'])) {

    if (isset($_POST['videoid']) && !empty($_POST['videoid']) && is_numeric($_POST['videoid']) && !empty(normal_text($_POST['videoid']))) {
        $video_id = normal_text($_POST['videoid']);
    } else {
        $errors[] = "Video ID is required";
    }

    if (isset($_POST['username']) && !empty($_POST['username']) && is_string($_POST['username']) && !empty(normal_text($_POST['username']))) {
        $username = normal_text($_POST['username']);
    } else {
        $errors[] = "Username is required";
    }


    if (empty($errors)) {

        $video = $a->get_video($video_id, $username);
        json_response(200, 'success', $video);



    } else {
        json_response(403, 'error', $errors);
    }

}

$errors[] = "Bad request parameter";
json_response(403, 'error', $errors);
