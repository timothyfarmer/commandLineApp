<?php namespace Acme;
 use Symfony\Component\Console\Input\InputArgument;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;

 class AddUserCommand extends Command {

   public function __construct()
   {
     parent::__construct();
   }

   /**
    * Configuration for add_user command.
    */
   public function configure()
   {
     $this->setName('add_user')
       ->setDescription('Add a new User')
       ->addArgument('name', InputArgument::REQUIRED)
       ->addArgument('cardNumber', InputArgument::REQUIRED)
       ->addArgument('limit', InputArgument::REQUIRED);

   }

   /**
    * @param InputInterface $input
    * @param OutputInterface $output
    *
    * Just using one table, 'Users', since this is a very small app
    * for demonstration purposes.
    */
   public function execute(InputInterface $input, OutputInterface $output)
   {
      //get args
      $name = $input->getArgument('name');
      $cardNumber = $input->getArgument('cardNumber');
      $limit = str_replace("$","",$input->getArgument('limit'));

      //store user in datasource
      $this->storeUser($name, $cardNumber, $limit);
   }

   /**
    * @param $name
    * @param $cardNumber
    * @param $limit
    * @param $is_valid
    *
    * Store user in datasource
    */
   private function storeUser($name, $cardNumber, $limit){

     //check if valid card
     $is_valid = (int) $this->isValidCreditCard($cardNumber);
     //store user
     DatabaseConnection::get()->addUser(compact('name', 'cardNumber', 'limit', 'is_valid'));
   }

   /**
    * @param $cardNumber
    * @return bool
    *
    * Validate credit card numbers using Luhn's mod 10 algorithm
    * returns true if valid false if not valid
    */
   private function isValidCreditCard($cardNumber){
     $sum = 0;
     $alternate = false;
     for($i = strlen($cardNumber) - 1; $i >= 0; $i--){
       if($alternate){
         $temp = $cardNumber[$i];
         $temp *= 2;
         if ($temp > 9) {
           $temp -= 9;
           $cardNumber[$i] = $temp;
         } else {
           $cardNumber[$i] = $temp;
         }
       }
       $sum += $cardNumber[$i];
       $alternate = !$alternate;
     }
     return $sum %10 == 0;
   }

 }