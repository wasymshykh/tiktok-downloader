<?php

require_once '../app/start.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

set_time_limit(0);

/* Check the authentication */
$auth = $a->check_auth();
if (!$auth['status']) {
    json_response(403, 'error', 'Permission denied');
}
$auth = $auth['data'];
/* //--- Auth check end */

$errors = [];
$logs = new Logs($db);

if (isset($_POST['save_video'])) {

    if (isset($_POST['video_id']) && !empty($_POST['video_id']) && is_numeric($_POST['video_id']) && !empty(normal_text($_POST['video_id']))) {
        $video_id = normal_text($_POST['video_id']);
    } else {
        $errors[] = "Video ID is required";
    }

    if (isset($_POST['username']) && !empty($_POST['username']) && is_string($_POST['username']) && !empty(normal_text($_POST['username']))) {
        $username = normal_text($_POST['username']);
    } else {
        $errors[] = "Username is required";
    }

    if (isset($_POST['author']) && !empty($_POST['author']) && is_numeric($_POST['author']) && !empty(normal_text($_POST['author']))) {
        $author_id = normal_text($_POST['author']);
    } else {
        $errors[] = "Author ID is required";
    }

    if (isset($_POST['nick']) && !empty($_POST['nick']) && is_string($_POST['nick']) && !empty(normal_text($_POST['nick']))) {
        $nick = normal_text($_POST['nick']);
    } else {
        $errors[] = "Author nick is required";
    }

    if (isset($_POST['thumb']) && !empty($_POST['thumb']) && is_string($_POST['thumb']) && !empty(normal_text($_POST['thumb']))) {
        $thumb = $_POST['thumb'];
    } else {
        $errors[] = "Author thumb picture is required";
    }

    if (isset($_POST['cover']) && !empty($_POST['cover']) && is_string($_POST['cover']) && !empty(normal_text($_POST['cover']))) {
        $cover = $_POST['cover'];
    } else {
        $errors[] = "Video cover picture is required";
    }

    if (isset($_POST['desc']) && !empty($_POST['desc']) && is_string($_POST['desc']) && !empty(normal_text($_POST['desc']))) {
        $description = normal_text($_POST['desc']);
    } else {
        $errors[] = "Video description is required";
    }

    if (isset($_POST['created']) && !empty($_POST['created']) && is_numeric($_POST['created']) && !empty(normal_text($_POST['created']))) {
        $created = normal_text($_POST['created']);
        $created = date("Y-m-d H:i:s", $created);
    } else {
        $errors[] = "Video created timestamp is required";
    }

    if (empty($errors)) {

        $video = $a->get_video($video_id, $username);

        if ($video['status']) {
            
            if (!empty($video['data'])) {

                try {
                    $video_stream = file_get_contents($video['data']);
                    $thumb_stream = file_get_contents($thumb);
                    $cover_stream = file_get_contents($cover);
                    
                    $video_file_name = DIR."saved/videos/$video_id.mp4";
                    $handle = fopen($video_file_name,"w+");
                    fwrite($handle, $video_stream);
                    fclose($handle);
    
                    $thumb_file_name = "$author_id.jpeg";
                    $thumb_file_path = DIR."saved/thumb/$thumb_file_name";
                    $handle = fopen($thumb_file_path,"w+");
                    fwrite($handle, $thumb_stream);
                    fclose($handle);
    
                    $cover_file_name = "$video_id.webp";
                    $cover_file_path = DIR."saved/dynamic/$cover_file_name";
                    $handle = fopen($cover_file_path,"w+");
                    fwrite($handle, $cover_stream);
                    fclose($handle);
                } catch (Exception $e) {
                    $logs->create("node_api", "Exception in save_video.php", json_encode($e->getMessage()));
                    json_response(400, 'error', ["Unable to save the file"]);
                }

                $r = $a->save_video_data($auth['user_id'], $video_id, $author_id, $username, $nick, $thumb_file_name, $description, $cover_file_name, $created, $video['data']);
                if ($r['status']) {
                    json_response(200, 'success', 'Successfully saved!');
                } else {
                    json_response(400, 'error', ["System error: 1006"]);
                }
            } else {
                $logs->create("node_api", "Exception in save_video.php", json_encode(['data' => $r['data']]));
                json_response(400, 'error', ["System error: 1005"]);
            }

        } else {
            $_e = ['data' => $video['data'], 'message' => $video['message']];
            if (isset($video['info'])) { $_e['info'] = $video['info']; }
            $logs->create("node_api", "Exception in save_video.php", json_encode($_e));
            json_response(400, 'error', [$video['data']]);
        }

    } else {
        json_response(403, 'error', $errors);
    }

} else if (isset($_POST['download_video'])) {
    
    if (isset($_POST['video_id']) && !empty($_POST['video_id']) && is_numeric($_POST['video_id']) && !empty(normal_text($_POST['video_id']))) {
        $video_id = normal_text($_POST['video_id']);
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
        if ($video['status']) {
            if (!empty($video['data'])) {
                json_response(200, 'success', $video['data']);
            } else {
                $logs->create("node_api", "Exception in save_video.php", json_encode(['data' => $r['data']]));
                json_response(400, 'error', ["System error: 1005"]);
            }
        } else {
            $_e = ['data' => $video['data'], 'message' => $video['message']];
            if (isset($video['info'])) { $_e['info'] = $video['info']; }
            $logs->create("node_api", "Exception in save_video.php", json_encode($_e));
            json_response(400, 'error', [$video['data']]);
        }

    } else {
        json_response(403, 'error', $errors);
    }

} else if (isset($_POST['remove_video'])) {

    if (isset($_POST['video_id']) && !empty($_POST['video_id']) && is_numeric($_POST['video_id']) && !empty(normal_text($_POST['video_id']))) {
        $video_id = normal_text($_POST['video_id']);

        // checking if video id is in db
        $r = $a->get_saved_video_by_id($auth['user_id'], $video_id);
        if ($r['status']) {
            $r = $r['data'];
        } else {
            $errors[] = "Video is not found";
        }
    } else {
        $errors[] = "Video ID is required";
    }

    if (empty($errors)) {

        // removing video file
        $video_file = DIR.'saved/videos/'.$r['video_id'].'.mp4';
        if (file_exists($video_file)) {
            unlink($video_file);
        }
        // removing thumb file
        $thumb_file = DIR.'saved/thumb/'.$r['video_author_picture'];
        if (file_exists($thumb_file)) {
            unlink($thumb_file);
        }
        // removing dynamic cover file
        $dynamic_file = DIR.'saved/dynamic/'.$r['video_cover'];
        if (file_exists($dynamic_file)) {
            unlink($dynamic_file);
        }

        $r = $a->remove_saved_video_by_id($auth['user_id'], $video_id);
        if ($r['status']) {
            json_response(200, 'success', ["Video has been removed"]);
        } else {
            json_response(403, 'error', ["Unable to remove video"]);
        }

    } else {
        json_response(403, 'error', $errors);
    }

}

$errors[] = "Bad request parameter";
json_response(403, 'error', $errors);
