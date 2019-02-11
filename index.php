<?php
date_default_timezone_set("UTC");
define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "0xhtml");
define("MYSQL_PASSWORD", "0xhtml");
define("MYSQL_DB", "0xhtml");
define("START_TEXT", "Welcome to 0xhtml (The Bashbin)

 * Webpage:    http://0xhtml.ddns.net
 * Source:     https://github.com/0xhtml/bashbin

  System information as of Mon Feb 30 19:54:12 UTC 2019

  System load:  14.2                Processes:         84
  Usage of /:   18.7% of 218.57GB   Users logged in:   0

 * Here you can store your bash commands and share them
     via a link to all of your friends.

Last login: Sun Feb 10 14:12:37 2019 from 192.168.178.188");
define("LINE_START", "\nuser@0xhtml:~$ ");
define("CURSOR", "▌");

require_once "classes/Commands.php";

$db = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

$commands = new Commands($db);

if (isset($_POST["bash"])) {
    $userCommands = $_POST["bash"];
    $userCommands = str_replace(["\r", "\n"], "", $userCommands);
    $userCommands = str_replace(CURSOR, "", $userCommands);
    $userCommands = str_replace(str_replace(["\r", "\n"], "", START_TEXT), "", $userCommands);
    $userCommands = str_replace(str_replace(["\r", "\n"], "", LINE_START), "\n", $userCommands);
    if (substr($userCommands, 0, 1) == "\n") {
        $userCommands = substr($userCommands, 1);
    }
    if ($commands->save($userCommands)) {
        header("Location: ?" . $commands->getToken());
        die();
    }
} else {
    if (isset($_SERVER["QUERY_STRING"])) {
        $userToken = $_SERVER["QUERY_STRING"];
        $userToken = trim($userToken);
        if (strlen($userToken) == 10) {
            $commands->load($userToken);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>0xhtml - The Bashbin</title>
    <meta name="description"
          content="With 0xhtml you can store your bash commands and share them via a link with your friends.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<form action="?" method="post">
    <div id="toolbar">
        <input type="submit" value="Save">
    </div>
    <textarea placeholder="0xhtml - The Bashbin" name="bash" id="bash" readonly><?= htmlspecialchars(START_TEXT . LINE_START . str_replace(["\r", "\n"], LINE_START, $commands->getCommands()) . CURSOR); ?></textarea>
</form>
<script>
    let cursor = "<?= htmlspecialchars(str_replace("\n", "\\n", CURSOR)) ?>";
    let linestart = "<?= htmlspecialchars(str_replace("\n", "\\n", LINE_START)) ?>";
</script>
<script src="js/script.js"></script>
</body>
</html>
