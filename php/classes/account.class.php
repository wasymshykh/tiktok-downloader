<?php

class Account {

    private $db;
    private $logs;
    private $class_name;
    private $class_name_lower;

    public function __construct(PDO $db) {
        $this->logs = new Logs($db);
        $this->db = $db;
        $this->class_name = "Account";
        $this->class_name_lower = "account_class";
    }

    public function verify_login ($username, $password)
    {
        $q = "SELECT * FROM `users` WHERE `user_username` = :u AND `user_password` = :p";
        $s = $this->db->prepare($q);
        $s->bindParam(":u", $username);
        $s->bindParam(":p", $password);
        if (!$s->execute()) {
            $failure = $this->class_name.'.verify_login - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }

        if ($s->rowCount() > 0) {
            return ['status' => true, 'type' => 'success', 'data' => $s->fetch()];
        }
        
        return ['status' => false, 'type' => 'empty', 'data' => 'No user found.'];
    }

    public function get_user_by_id ($id)
    {
        $q = "SELECT * FROM `users` WHERE `user_id` = :i";
        $s = $this->db->prepare($q);
        $s->bindParam(":i", $id);
        if (!$s->execute()) {
            $failure = $this->class_name.'.get_user_by_id - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }
        if ($s->rowCount() > 0) {
            return ['status' => true, 'type' => 'success', 'data' => $s->fetch()];
        }
        return ['status' => false, 'type' => 'empty', 'data' => 'No user found.'];
    }

    public function check_auth ()
    {
        if (!isset($_SESSION['logged']) || empty($_SESSION['logged']) || !is_numeric($_SESSION['logged'])) {
            return ['status' => false];
        }
        $u = $this->get_user_by_id($_SESSION['logged']);
        if (!$u['status']) {
            return ['status' => false];
        }
        return $u;
    }

    public function search_hashtag ($hashtag, $page)
    {
        try {
            $headers = array('Accept' => 'application/json');
            $request = Requests::get(PYTHON_HOST."/hashtag/$page/$hashtag", $headers, ['timeout' => 60]);
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                return ['status' => true, 'data' => $body->data];
            } else {
                return ['status' => false, 'data' => $body->data, 'message' => $body->message];
            }
        } catch (Exception $e) {
            return ['status' => false, 'data' => "Unable to retrive results within time limit", 'message' => "Python Process"];
        }
    }

    public function search_profile ($profile_name)
    {
        try {
            $headers = array('Accept' => 'application/json');
            $request = Requests::get(PYTHON_HOST."/profile/$profile_name", $headers, ['timeout' => 60]);
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                return ['status' => true, 'data' => $body->data];
            } else {
                return ['status' => false, 'data' => $body->data, 'message' => $body->message];
            }
        } catch (Exception $e) {
            return ['status' => false, 'data' => "Unable to retrive results within time limit", 'message' => "Python Process"];
        }
    }

    public function search_video ($profile_name, $video_id)
    {
        try {
            $headers = array('Accept' => 'application/json');
            $request = Requests::get(PYTHON_HOST."/video/$profile_name/$video_id", $headers, ['timeout' => 60]);
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                if (isset($body->data->itemInfo->itemStruct)) {
                    return ['status' => true, 'data' => [$body->data->itemInfo->itemStruct]];
                } else {
                    return ['status' => false, 'data' => 'Unable to find the video', 'message' => '404'];
                }
            } else {
                return ['status' => false, 'data' => $body->data, 'message' => $body->message];
            }
        } catch (Exception $e) {
            return ['status' => false, 'data' => "Unable to retrive results within time limit", 'message' => "Python Process"];
        }
    }

    public function get_video ($id, $username)
    {
        try {
            $headers = ['Accept' => 'application/json'];
            $request = Requests::get(NODE_HOST."/get/$username/$id", $headers, ['timeout' => 60]);
            
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                return ['status' => true, 'data' => $body->download];
            } else {
                return ['status' => false, 'data' => "Unable to get video download link", 'message' => $body->message];
            }
        } catch (Exception $e) {
            return ['status' => false, 'data' => "Unable to retrive results within time limit", 'info' => $e->getMessage(), 'message' => "Node Process"];
        }
    }

    public function save_video_data ($user_id, $video_id, $author_id, $username, $nick, $author_picture, $description, $cover, $created, $original)
    {
        $v = "video_";
        $q = "INSERT INTO `videos` (`{$v}id`, `{$v}user_id`, `{$v}author_id`, `{$v}author_username`, `{$v}author_nick`, `{$v}author_picture`, `{$v}cover`, `{$v}desc`, `{$v}dated`, `{$v}original`, `{$v}created`) VALUE ";
        $q .= "(:i, :u, :ai, :au, :an, :ap, :c, :d, :da, :o, :dt)";

        $s = $this->db->prepare($q);
        $s->bindParam(":i", $video_id);
        $s->bindParam(":u", $user_id);
        $s->bindParam(":ai", $author_id);
        $s->bindParam(":au", $username);
        $s->bindParam(":an", $nick);
        $s->bindParam(":ap", $author_picture);
        $s->bindParam(":c", $cover);
        $s->bindParam(":d", $description);
        $s->bindParam(":da", $created);
        $s->bindParam(":o", $original);
        $dt = current_date();
        $s->bindParam(":dt", $dt);

        if (!$s->execute()) {
            $failure = $this->class_name.'.save_video_data - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }

        return ['status' => true, 'type' => 'success'];
    }

    public function get_saved_videos ($user_id)
    {
        $q = "SELECT * FROM `videos` WHERE `video_user_id` = :i";
        $s = $this->db->prepare($q);
        $s->bindParam(":i", $user_id);
        if (!$s->execute()) {
            $failure = $this->class_name.'.get_saved_videos - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }
        if ($s->rowCount() > 0) {
            return ['status' => true, 'type' => 'success', 'data' => $s->fetchAll()];
        }
        return ['status' => false, 'type' => 'empty', 'data' => 'No videos found.'];
    }

    public function get_saved_video_by_id ($user_id, $video_id)
    {
        $q = "SELECT * FROM `videos` WHERE `video_user_id` = :i AND `video_id` = :v";
        $s = $this->db->prepare($q);
        $s->bindParam(":i", $user_id);
        $s->bindParam(":v", $video_id);
        if (!$s->execute()) {
            $failure = $this->class_name.'.get_saved_video_by_id - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }
        if ($s->rowCount() > 0) {
            return ['status' => true, 'type' => 'success', 'data' => $s->fetch()];
        }
        return ['status' => false, 'type' => 'empty', 'data' => 'No video found.'];
    }

    public function remove_saved_video_by_id ($user_id, $video_id)
    {
        $q = "DELETE FROM `videos` WHERE `video_user_id` = :i AND `video_id` = :v";
        $s = $this->db->prepare($q);
        $s->bindParam(":i", $user_id);
        $s->bindParam(":v", $video_id);
        if (!$s->execute()) {
            $failure = $this->class_name.'.remove_saved_video_by_id - E.02: Failure';
            $this->logs->create($this->class_name_lower, $failure, json_encode($s->errorInfo()));
            return ['status' => false, 'type' => 'query', 'data' => $failure];
        }
        if ($s->rowCount() > 0) {
            return ['status' => true, 'type' => 'success'];
        }
        return ['status' => false, 'type' => 'empty'];
    }

}
