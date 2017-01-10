<?php namespace Acme;
 use Acme\Command;
 use Symfony\Component\Console\Input\InputArgument;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;

 class AddUserCommand extends Command {

   public function __construct(DatabaseAdapter $database)
   {
     parent::__construct($database);
   }

   public function configure()
   {
     $this->setName('add_user')
       ->setDescription('Add a new User')
       ->addArgument('name', InputArgument::REQUIRED)
       ->addArgument('cardNumber', InputArgument::REQUIRED)
       ->addArgument('limit', InputArgument::REQUIRED);

   }

   public function execute(InputInterface $input, OutputInterface $output)
   {
      $name = $input->getArgument('name');
      $cardNumber = $input->getArgument('cardNumber');
      $is_valid = (int) $this->isLuhn10($cardNumber);
      $limit = $input->getArgument('limit');
      $this->database->query('INSERT INTO Users (`name`, `ccnum`, `limit`, `is_valid`) values(:name, :cardNumber, :limit, :is_valid)',
                              compact('name', 'cardNumber', 'limit', 'is_valid'));
   }

   public function isLuhn10($cardNumber){
     $sum = 0;
     $alternate = false;
     for($i = strlen($cardNumber) - 1; $i >= 0; $i--){
       if($alternate){
         $temp = $cardNumber[$i];
         $temp *= 2;
         $cardNumber[$i] = ($temp > 9) ? $temp -= 9 : $temp;
       }
       $sum += $cardNumber[$i];
       $alternate = !$alternate;
     }
     return $sum %10 == 0;
   }
 }