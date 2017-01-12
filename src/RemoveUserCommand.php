<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 1/11/2017
 * Time: 5:39 PM
 */

namespace Acme;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUserCommand extends Command
{
  public function __construct()
  {
    parent::__construct();
  }

  public function configure()
  {
    $this->setName('remove_user')
      ->setDescription('Remove a user')
      ->addArgument('name', InputArgument::REQUIRED);
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    $name = $input->getArgument('name');
    $this->removeUser($name);
  }

  public function removeUser($name){
    DatabaseConnection::get()->removeUserByName($name);
  }

}