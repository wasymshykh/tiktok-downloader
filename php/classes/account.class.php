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

}
