<?php

class Data
{

    public $server;
    public $user;
    public $password;
    public $db;
    public $connection;
    public $port;
    public $isActive;

    public function __construct()
    {
        $hostName = gethostname();

        switch ($hostName) {

            default:
                 $this->isActive = false;
                                $this->server = "localhost";
                                $this->user = "root";
                                $this->password = "";
                                $this->db = "bdgym";

        }
    }
}

?>