<?php namespace Acme;

use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
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

  public function execute(InputInterface $input, OutputInterface $output)
  {
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
          case "Credit":
            $this->callCreditCommand($line, $output);
            break;
        }
      }
    } catch (Exception $e){
      $output->writeln($e->getMessage());
    }
    //getting rid of data because this program isn't something to reuse
    DatabaseConnection::get()->truncate();
    //return $output->writeln("file name is " . $file_name);
  }

  public function callAddUserCommand($line, OutputInterface $output){
    $command = $this->getApplication()->find('add_user');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1],
      'cardNumber' => $line[2],
      'limit' => $line[3]
    );
    $addInput = new ArrayInput($arguments);
    $command->run($addInput, $output);
  }

  public function callCreditCommand($line, OutputInterface $output) {
    $command = $this->getApplication()->find('credit');
    $line = explode(" ", $line);
    $arguments = array('name' => $line[1], 'amount' => $line[2]);
    $creditInput = new ArrayInput($arguments);
    $command->run($creditInput, $output);
  }
}