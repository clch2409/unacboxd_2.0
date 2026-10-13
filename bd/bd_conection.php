<?php

class DataBase{
  
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "unacboxd";

    private string $string_conection;

    function __construct(){
        $this->string_conection = "mysql:host=$this->host;dbname=$this->database";
    }

    public function conectar(){
        $con = new PDO($this->string_conection, $this->user, $this->password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }
}


?>