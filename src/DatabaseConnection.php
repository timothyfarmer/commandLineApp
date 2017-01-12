<?php namespace Acme;

use Exception;
use PDO;

/**
 * Class DatabaseConnection
 * @package Acme
 * Simple DB singleton to run basic queries that we need.
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

  public function addUser($parameters){
    $sql = 'INSERT INTO Users (`name`, `ccnum`, `limit`, `is_valid`) values(:name, :cardNumber, :limit, :is_valid)';
    return $this->connection->prepare($sql)->execute($parameters);
  }

  public function fetchUserByName($name){
    $row = null;
    $result = $this->connection->query('SELECT * FROM Users WHERE Users.Name = "' . $name . '"', PDO::FETCH_ASSOC);
    if($result){
      $row = $result->fetch(PDO::FETCH_ASSOC);
    }
    return $row;
  }

  public function updateBalanceForUser($user){
    $result = $this->connection->query('UPDATE Users SET Balance="' . $user['Balance'] . '" WHERE Users.id = "' . $user['id'] . '"');
    return $result;
  }

  public function truncate(){
    $sql = 'DELETE from Users';
    return $this->connection->prepare($sql)->execute();
  }

  public function removeUserByName($name){
    $result = $this->connection->query('DELETE FROM Users WHERE Users.Name = "' . $name . '"');
    return $result;
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
    return $rows;
  }
}