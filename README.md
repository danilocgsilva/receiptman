# Receiptman

[Development patterns](#Development-patterns)

The application is a *Docker receipt maker*.

To make a Docker receipt, that are some steps by which the application flows.

Know that this is a command line application.

This application uses the Symfony as a framework.

The application startup happens right in the project root, through rhe `receipt` file. To start the program, you have a few options:

**1. Through bash access**

The first line from `receipt` have the sign to bash that this is a PHP script.

```
#!/usr/bin/env php
```

This allow to use as a normal bash script. To run the application, do in command line:

```
./receipt
```

**2. Explicitly accessing the php interpreter through the command line**

Once inside the environmento, run:

```
php receipt
```

**3. Through local machine**

You can use the script outside the container. Just run:

```
docker exec -it receiptman /var/www/receipt
```
**4. Through local machine using convenient shell**

(Possible only on a Unix like system, eg. Mac, Linux)

In the project root, you can run outside the container the following script located in the project root:
```
./receiptman
```

## Development patterns

The application flow starts at the `receipt` script, which is based on Symfony.

### First flow layer: *Commands*

The lowest level relies on *Commands*, which are the references all loaded in the very first file. They are based on `Symfony\Component\Console\Command\Command`. So it depends upon commands patterns provided by the framework and represents the *gate* to the application rules.

#### Available commands:

* [PhpFullDevCommand](www/src/Command/PhpCommand.php)
* [PhpCommand](www/src/Command/PhpCommand.php)
* [PythonCommand](www/src/Command/PythonCommand.php)
* [NodeCommand](www/src/Command/NodeCommand.php)
* [NginxCommand](www/src/Command/NginxCommand.php)
* [ApacheCommand](www/src/Command/ApacheCommand.php)
* [DotNetCommand](www/src/Command/DotNetCommand.php)
* [PostgreCommand](www/src/Command/PostgreCommand.php)

### Second flow layer: *Receipts*

**Couped to the *commands***, we have the *receipts*. Those are the objects responsible to the environment rules. They are responsible to the details of receipts, and privides method to allow the environment customization.

### Glue layer: *Questions*

Another important type of object for the application flow are the *Questions*. These are the classes that link the Symfony command interface to the *receipts*. In another words, the questions translates Symfony question into their respective receipt methods, allowing the user customize the receipt through the questions interface provided by Symfony. It' is also worth to mention that Questions are *tightly coupled with Commands and Receitps. So for each environment, we have the trio that must works and exists together:

```
Commands <--- Questions ---> Receipt
```
