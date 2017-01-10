<?php

namespace Acme;


use Exception;
use PDO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class CreditCardApplication extends Application
{
  /**
   * @var DatabaseAdapter
   */
  protected $dbAdapter;

  public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
  {
    try {
      $pdo = new PDO('sqlite:db.sqlite');

      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $ex){
      echo 'Cannot connect to database';
      exit(1);
    }
    $dbAdapter = new DatabaseAdapter($pdo);
    $this->dbAdapter = $dbAdapter;
    parent::__construct($name, $version);
  }

  protected function getCommandName(InputInterface $input)
  {
    return 'command';
  }

  protected function getDefaultCommands()
  {
    $defaultCommands[] = new Command($this->dbAdapter);
    $defaultCommands[] = new AddUserCommand($this->dbAdapter);
    return $defaultCommands;
  }

}