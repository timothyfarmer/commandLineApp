<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 1/11/2017
 * Time: 8:40 AM
 */

namespace Acme;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreditUserCommand extends Command
{
  public function __construct()
  {
    parent::__construct();
  }

  public function configure()
  {
    $this->setName('credit_user')
      ->setDescription('Decrease a users balance by {amount}')
      ->addArgument('name', InputArgument::REQUIRED)
      ->addArgument('amount', InputArgument::REQUIRED);
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    //get args
    $name = $input->getArgument('name');
    $amount = $input->getArgument('amount');

    // update the users balance
    $this->changeUserBalance($name, $amount, $this->getName());
  }

}