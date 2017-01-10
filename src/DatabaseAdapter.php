<?php namespace Acme;

use PDO;

class DatabaseAdapter {
  protected $connection;

  function __construct(PDO $connection){
    $this->connection = $connection;
  }

  public function query($sql, $parameters){
    return $this->connection->prepare($sql)->execute($parameters);
  }

  public function fetchUserByName($name){
    return $this->query('SELECT * FROM Users WHERE NAME = ' . $name);
  }

  public function fetchAllUsers(){
    return $this->query('SELECT * FROM Users');
  }
}