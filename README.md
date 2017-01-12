Credit Card Processing
-----
 
Implement a program that will add new credit card accounts, process charges and credits
against them, and display balance.
 
## Requirements:
 
- Program must accept input from two sources: a filename passed in
  command line arguments and STDIN. For example, on Linux or OSX both
- Program should accept four input commands passed with space delimited
  arguments.
 
- "Add" will create a new credit card account for a given name, card number, and limit
   - Card numbers should be validated using Luhn 10
   - New cards start with a $0 balance
- "Charge" will increase the balance of the card associated with the provided
  name by the amount specified
   - Charges that would raise the balance over the limit are ignored as if they
     were declined
   - Charges against Luhn 10 invalid cards are ignored
- "Credit" will decrease the balance of the card associated with the provided
  name by the amount specified
   - Credits that would drop the balance below $0 will create a negative balance
   - Credits against Luhn 10 invalid cards are ignored
- “Remove” will delete the account of that person
 
- When all input has been read and processed, a summary should be generated and
  written to STDOUT.
- The summary should include the name of each person followed by a colon and
  balance
- The names should be displayed alphabetically
- Display "error" instead of the balance if the credit card number does not pass
  Luhn 10
 
## Input Assumptions:
 
- All input will be valid.
- All input will be space delimited.
- Credit card numbers will always be numeric.
- Amounts will always be prefixed with "$" and will be in whole dollars (no
  decimals).
- There will not be any duplicate names.
 
## Example Input:
 
```
Add Terry 4111111111111111 $1500
Add Lowell 5454545454545454 $2000
Add Quinton 1234567890123456 $1000
Add Anthony 5500000000000004 $5000
Charge Terry $500
Charge Terry $800
Charge Lowell $10
Credit Lowell $100
Credit Quinton $500
Charge Terry $1000
Remove Anthony
```
 
## Example Output:
 
```
Lowell: $-90
Quinton: error
Terry: $1300
```
##Installation
You should have composer installed along with php. You can read about [Composer here](https://getcomposer.org/doc/00-intro.md)
Also, you can read about Symfony 2.8 system requirements [here](http://symfony.com/doc/2.8/reference/requirements.html). Make sure to enable sqlite. Windows: ```extension=php_pdo_sqlite.dll``` *nix: ```extension=php_pdo_sqlite.so``` You can read more about it [here].(http://php.net/manual/en/pdo.installation.php) To install this small project just clone this github repo by running ```https://github.com/timothyfarmer/commandLineApp.git commandLineApp``` into your favorite dev folder and then run: ```composer install```

You can then run the program by piping your input file via standard input  ```./program < input.txt``` or by passing the file name ```./program input.txt```

**Note: if you use piping, you must supply a valid file (for the meantime) or the program will hang as it is waiting for STDIN to provide input with a valid EOF signal**

That's all there is to it! Easy right?

##Database
The database for this console application is a very simple one used to persist
data across the app. Since we only need to store a small number of data points,
I decided to keep them all in one table, 'Users'. 

The Users table is as follows:

| Users |  |
| ----------- | --------- |
| id          | INTEGER   |
| Name | INTEGER |
| CCNum | INTEGER |
| Limit | INTEGER |
| is_valid | INTEGER |
| Balance | INTEGER |

I decided not to use a separate table for credit cards just to keep it simple for a small app such as this.

The [DatabaseConnection](src/DatabaseConnection.php) class is a simple use of the sqlite PDO (PHP Data Objects) class to access our sqlite
database which I have implemented as a singleton that provides a static get() method to quickly return the connection
to run methods against.
The methods available are:

| Method Name | Return Type | Arguments | Functionality |
| ----------- | ----------- | ----------| ------------- |
| addUser($parameters) | Boolean | **$parameters:** (array) | adds a user to the database |
| fetchUserByName($name) | Array | **$name:** (string) | gets a user from the database from their Name |
| updateBalanceForUser($user) | Boolean | **$user:** (array) | sets the Balance for the user |
| truncate(void) | Boolean | void | deletes all users from the database |
| fetchAllUsers(void) | Array | void | returns fetched Users rows as array of arrays |

##Application Design
I picked PHP for this project because it is my language of choice that I use on a daily basis and also because it provides (via composer) very easy dependency management and installation between developers, also because of the ease of use of Symfony Console which is very handy in a lot of the cutting-edge PHP frameworks out there such as Symfony and Laravel. Learning to use Symfony Console will benefit you greatly and if you don't believe me, check out [Laravel Artisan](https://laravel.com/docs/5.3/artisan) which leverages a little of Symfony Console. I'm using a back-dated (2.8) version of Symfony here because I've used it a little before and already had it on my machine (in case you were wondering).

The main point of entry is in the program* file which requires our dependencies in the vendor directory and "new ups" the [CreditCardApplication](src/CreditCardApplication.php)

[CreditCardApplication](src/CreditCardApplication.php) just sets the default commands that will be used via the input.txt file and runs the top command which I simply named [Command](src/Command.php).

[Command](src/Command.php) reads the input.txt file and runs the sub-commands in the order that input.txt presents them. Each sub-command persists data using the [DatabaseConnection](src/DatabaseConnection.php) 

**Chain of Responsibility Pattern** is used in [CreditUserCommand](src/CreditUserCommand.php) and [ChargeUserCommand](ChargeUserCommand.php). Since neither [CreditUserCommand](src/CreditUserCommand.php) nor [ChargeUserCommand](ChargeUserCommand.php) can handle the method call ```$this->changeUserBalance()``` and also ```$this->getName()``` these method calls get moved up the object "chain" until they find an object that can handle the request which will be my implementation of [Command](Command.php) and Symfony's implementation of ``Symfony\Component\Console\Command\Command``` respectfully. 

**Singleton Pattern** is used on the [DatabaseConnection](src/DatabaseConnection.php) that gives us a static (one and only one instance) 
object so that we can interact with our database without having to open and close connections all the time.

This was a great way to learn Symfony Commands and I plan on writing a lot more to increase my productivity! Enjoy!

You can read all about [Symfony Commands here](https://symfony.com/doc/current/console.html)
