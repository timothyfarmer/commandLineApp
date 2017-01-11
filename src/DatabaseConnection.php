<?php namespace Acme;

use PDO;

/**
 * Class DatabaseConnection
 * @package Acme
 * Simple DB adapter to run basic queries that we need
 */
class DatabaseConnection {
  protected $connection;

  private function __construct(){
    try {
      $pdo = new PDO('sqlite:db.sqlite');

      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $ex){
      echo 'Cannot connect to database';
      exit(1);
    }
    $this->connection = $pdo;
  }

  public static function get(){
    static $database = null;
    if($database == null){
      $database = new DatabaseConnection();
    }
    return $database;
  }

  public function insert($sql, $parameters){
    return $this->connection->prepare($sql)->execute($parameters);
  }

  public function fetchUserByName($name){
    return $this->connection->query('SELECT * FROM Users WHERE NAME = ' . $name);
  }

  public function truncate(){
    $sql = 'DELETE from Users';
    return $this->connection->prepare($sql)->execute();
  }

  public function fetchAllUsers(){
    $sql = 'SELECT * FROM Users';
    $result = $this->connection->query($sql,PDO::FETCH_ASSOC);
    $rows = array();
    if($result){
      while($row = $result->fetch(PDO::FETCH_ASSOC)){
        $rows[] = $row;
      }
    }
    var_dump($rows);
    return $rows;
  }
}