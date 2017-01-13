<?php namespace Acme;

use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package Acme
 *
 * This is the default command class that we will be using
 * to parse through the files and call the sub-commands
 * based on the input file.
 */
class Command extends SymfonyCommand {

  public function __construct()
  {
    parent::__construct();
  }

  public function configure()
  {
    $this->setName('command');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * Simple execute command that reads the input file,
   * runs commands, and prints the summary to console
   */
  public function execute(InputInterface $input, OutputInterface $output)
  {
    $this->readInputFileAndRunCommands($input, $output);
    $this->printSummaryToConsole($output);
  }

  /**
   * @param InputInterface $input
   * This is the meat and potatoes that parses the input file
   * and runs the commands line-by-line
   */
  private function readInputFileAndRunCommands(InputInterface $input, OutputInterface $output){
    $file_name = $input->getFirstArgument();
    $handle = null;
    try {
      if($file_name){
        $handle = fopen($file_name, 'r');
      } else {
        $handle = fopen('php://stdin', 'r');
      }
      while (($line = fgets($handle)) !== false) {
        $command = explode(" ", $line)[0];
        switch ($command) {
          case "Add":
            $this->callAddUserCommand($line, $output);
            break;
          case "Remove":
            $this->callRemoveUserCommand($line, $output);
            break;
          case "Charge":
            $this->callChargeUserCommand($line, $output);
            break;
          case "Credit":
            $this->callCreditUserCommand($line, $output);
            break;
        }
      }
    } catch (Exception $e){
      $output->writeln($e->getMessage());
    }
  }

  private function printSummaryToConsole(OutputInterface $output){
    //fetch all users
    $users = DatabaseConnection::get()->fetchAllUsers();

    foreach($users as $user){
      if($user['is_valid']){
        $output->writeln($user['Name'] . ": $" . $user['Balance']);
      } else {
        $output->writeln($user['Name'] . ": error");
      }
    }
    DatabaseConnection::get()->truncate();
  }

  private function callAddUserCommand($line, OutputInterface $output){
    $command = $this->getApplication()->find('add_user');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1],
      'cardNumber' => $line[2],
      'limit' => $line[3]
    );
    $input = new ArrayInput($arguments);
    $command->run($input, $output);
  }

  private function callRemoveUserCommand($line, OutputInterface $output)
  {
    $command = $this->getApplication()->find('remove_user');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1]);
    $input = new ArrayInput($arguments);
    $command->run($input, $output);
  }

  private function callCreditUserCommand($line, OutputInterface $output) {
    $command = $this->getApplication()->find('credit_user');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1], 'amount' => $line[2]);
    $input = new ArrayInput($arguments);
    $command->run($input, $output);
  }

  private function callChargeUserCommand($line, OutputInterface $output){
    $command = $this->getApplication()->find('charge_user');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1], 'amount' => $line[2]);
    $input = new ArrayInput($arguments);
    $command->run($input,$output);
  }

  protected function changeUserBalance($name, $amount, $type)
  {
    $amount = str_replace("$", "", $amount);
    $user = DatabaseConnection::get()->fetchUserByName($name);
    if((int)$user['is_valid']){
      if($type == 'credit_user'){
        $user['Balance'] -= $amount;
      } elseif($type == 'charge_user'){
        $user['Balance'] += $amount;
      }
    } else {
      return;
    }
    if($user['Balance'] < $user['Limit']) {
      DatabaseConnection::get()->updateBalanceForUser($user);
    }
  }
}