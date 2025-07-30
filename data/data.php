<?php

class Data {

    public $server;
    public $user;
    public $password;
    public $db;
    public $connection;
    public $isActive;

    public function __construct() {
        $hostName = gethostname();

        switch ($hostName) {
            case "deibisgm-HP-ENVY-x360-Convertible-15-ee1xxx":
                $this->isActive = false;
                $this->server = "127.0.0.1";
                $this->user = "deibisgm";
                $this->password = "Jdgm5171";
                $this->db = "gimnasio_db";
                break;


            default:
                 $this->isActive = false;
      			 $this->server = "x.x.x.x";
      			 $this->user = "xxxxxxx";
      			 $this->password = "xxxxxxx";
      			 $this->db = "xxxxxxxxxx";
                break;
        }
    }
}
?>