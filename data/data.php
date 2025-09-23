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
            case "deibisgm-HP-ENVY-x360-Convertible-15-ee1xxxa":
                $this->isActive = false;
                $this->server = "127.0.0.1";
                $this->user = "root";
                $this->password = "6453";
                $this->db = "dbgym";
                break;

            case "yei-Inspiron-3501j":
                $this->isActive = false;
                $this->server = "127.0.0.1";
                $this->user = "yei";
                $this->password = "6453";
                $this->db = "dbgym";
                break;

            /*case "ciany-Inspiron-15-3515":
                $this->isActive = false;
                $this->server = "127.0.0.1";
                $this->user = "ciany";
                $this->password = "1223";
                $this->db = "dbgym";
                break;
*/
            default:
                $this->isActive = false;
                $this->server = "localhost";
                $this->user = "root";
                $this->password = "";
                $this->db = "bdgym";

                break;
        }
    }
}

?>
