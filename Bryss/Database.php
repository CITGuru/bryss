<?php

namespace Bryss;

class Database {

    private $host ;
    private $username;
    private $password ;
    private $database;

    public $connection;


    function __construct($host, $username, $password, $database){


        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $conn = $this->getConnection();

        return $conn;
    }

    // get the database connection
    public function getConnection(){

        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
            $this->connection->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Error: " . $exception->getMessage();
        }

        return $this->connection;
    }

    public function query($query, $data=array()){
        $prep = $this->connection->prepare($query);
        $prep->execute($data);
        $result = $prep->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function queryFirstRow($query, $data=array()){
        $prep = $this->connection->prepare($query);
        $prep->execute($data);
        $result = $prep->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function queryFirstField($query, $data=array()){
        $result = $this->queryFirstRow($query, $data);
        if($result){
            return array_values($result)[0];
        }
        return $result;
    }

    public function sanitizeSQL($qs){
        return mysql_real_escape_string($qs);
    }

    public function queryDB($query){
        $prep = $this->connection->query($query);
        $result = $prep->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}