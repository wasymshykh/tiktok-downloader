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
            $request = Requests::get(PYTHON_HOST."/$page/$hashtag", $headers);
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                return ['status' => true, 'data' => $body->data];
            } else {
                return ['status' => false, 'data' => $body->data, 'message' => $body->message];
            }
        } catch (Exception $e) {
            die('Please check, python process is not running.');
        }

    }

    public function get_video ($id, $username)
    {
        
        try {
            $headers = array('Accept' => 'application/json');
            $request = Requests::get(NODE_HOST."/get/$username/$id", $headers);
            
            $body = json_decode($request->body);

            if ($body->message === 'Success') {
                return ['status' => true, 'data' => $body->download];
            } else {
                return ['status' => false, 'data' => "Unable to get video download link", 'message' => $body->message];
            }
        } catch (Exception $e) {
            die('Please check, node process is not running.');
        }

    }

}
