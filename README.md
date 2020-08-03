# snowtricks
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/9568076513904fbdae68af9698eadffa)](https://app.codacy.com/manual/FrancisLibs/snowtricks?utm_source=github.com&utm_medium=referral&utm_content=FrancisLibs/snowtricks&utm_campaign=Badge_Grade_Dashboard)

SnowTricks - Projet6 - 
This site was created in order to study the Symfony framework (V4.4), as part of an application developer training. The subject is snowboarding. 
The principal goal of this training is to learn the development with Symfony’s framework.

Environnement 
-   WampServer 3.2.3.0
-	  Apache 2.4.41
-	  PHP 7.4.6
-	  MySQL 8.0.18
-	  Composer 1.10.8
-	  Git 2.27
-	  Symfony 4.4.1
-	  JQuery 3.4.1
-	  Bootstrap 4.4.1

Environnement
The installation of an apache environment, with min. PHP 7.4 is necessary to work with Symfony 4.4.
Notice : Several extensions of PHP must be activated.

Composer is needed to install Symfony and his components. (<https://getcomposer.org/>)

Git is not necessary but he facilitate the download from GitHub by your system. (<https://git-scm.com/downloads>)

Files deployments
It Is possible to use 2 different methods:
-	  By “hand”: copy the entire repository from GitHub to your pc repository. 
Repository by GitHub address: <https://github.com/FrancisLibs/snowtricks>

-	  Or (easier) clone the repository from GitHub by your Pc with a git command(it need to install first Git) : <https://github.com/FrancisLibs/snowtricks.git>
After installing the files, it is necessary to install the dependencies. Use the composer command: composer install. 

Database 
To inform Symfony what’s the database name and other accessing informations so the connection name and password, the file .env, in the root directory of the project,  is to be modified: Looking for the line that begin with DATABASE and give the right information’s to access on your database.
For example: DATABASE_URL=mysql://root:password@127.0.0.1:3308/snowtricks?serverVersion=8.0.18
Where “root” is the connection name, “password” is the connection password (It can be blank) and “snowtricks” is the database name.

If you encounter some problems, feel free to visit the doctrine site: <https://symfony.com/doc/4.4/doctrine.html>

For the creation of the database:
php bin/console doctrine:database:create

Open a new console, and create the database with 2 commands:
-	  php bin/console doctrine:migrations:diff 
-	  php bin/console doctrine:migrations:migrate

If the first command not works, verify if the php -ver command is functional and show the version of php. If the command not work, be sure that the system variable PATH, contain the php.exe route. 

At the end of the procedure, use the fixtures to load fake data to the data base.
To load this first data, use this command:
php bin/console doctrine:fixtures:load 

Mailer address
The .env file is to modify:
Find the line that begin with Mailer and modify it with your personal data :
Per example:
MAILER_URL=gmail://yourEmaiAdress:@localhost
Take care, but by using an gmail address, you need an application code, who’s give you access to send email with your account.

For more info, see <https://symfony.com/doc/current/mailer.html#transport-setup>

Finish: run the application

The Apache/Php runtime environment must be start by using the command:
php bin/console server:run
The URL "http://localhost:8000" is the address who’s listen the symphony site.

By a virtualhost
If you don't want to use WebServerBundle, you can use your Wamp (or other) environment in a normal way.
This by configuring a virtual host.
Then check "http://localhost".

Users accounts
Several users have been created. Their names are :
Marc, jean, eric, sophie, marie.
All with the same password: “password”
