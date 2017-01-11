<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 1/11/2017
 * Time: 8:40 AM
 */

namespace Acme;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreditCommand extends Command
{
  public function __construct()
  {
    parent::__construct();
  }

  public function configure()
  {
    $this->setName('credit')
      ->setDescription('Decrease a users balance by {amount}')
      ->addArgument('name', InputArgument::REQUIRED)
      ->addArgument('amount', InputArgument::REQUIRED);
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    //get args
    $name = $input->getArgument('name');
    $amount = $input->getArgument('amount');

    //store user in datasource
    $this->decreaseUserBalance($name, $amount);
  }

  public function decreaseUserBalance($name, $amount){
    DatabaseConnection::get()->fetchAllUsers();
  }

}