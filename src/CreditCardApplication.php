<?php

namespace Acme;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class CreditCardApplication extends Application
{

  public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
  {
    parent::__construct($name, $version);
  }

  /**
   * @param InputInterface $input
   * @return string
   *
   * returns the command name
   */
  protected function getCommandName(InputInterface $input)
  {
    return 'command';
  }

  /**
   * @return array
   *
   * setting up commands that will be used at runtime.
   */
  protected function getDefaultCommands()
  {
    $defaultCommands[] = new Command();
    $defaultCommands[] = new AddUserCommand();
    $defaultCommands[] = new CreditUserCommand();
    $defaultCommands[] = new RemoveUserCommand();
    $defaultCommands[] = new ChargeUserCommand();
    return $defaultCommands;
  }

}