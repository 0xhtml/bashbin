<?php
// Timezone
date_default_timezone_set("UTC");

// MySQL-Connection
define("MYSQL_HOST",     "");
define("MYSQL_USER",     "");
define("MYSQL_PASSWORD", "");
define("MYSQL_DB",       "");

// Editor configuration
define("LINE_START", "\nuser@bashbin:~$ ");
define("START_TEXT", "Welcome to the Bashbin

 * Webpage:    http://bashbin.ml
 * Source:     https://github.com/0xhtml/bashbin

  System information as of " . date("D M n H:i:s e Y") . "

  System load:  14.2                Processes:         84
  Usage of /:   18.7% of 218.57GB   Users logged in:   0

 * Here you can store your bash commands and share them
     via a link to all of your friends.
");
