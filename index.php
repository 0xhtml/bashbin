<?php
date_default_timezone_set("UTC");
define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "0xhtml");
define("MYSQL_PASSWORD", "0xhtml");
define("MYSQL_DB", "0xhtml");

if (isset($_COOKIE["last_login_1"]) and !isset($_COOKIE["session_1"])) {
    $login = "\nLast login: " . $_COOKIE["last_login_1"];
    setcookie("last_login_1", $_COOKIE["last_login_1"], 0, "/");
    if (!isset($_COOKIE["last_login_2"])) {
        setcookie("last_login_2", date("D M n H:i:s e Y") . " from " . $_SERVER['REMOTE_ADDR'], time() + 30758400, "/");
    }
    setcookie("session_2", "1", 0, "/");
} elseif (isset($_COOKIE["last_login_2"]) and !isset($_COOKIE["session_2"])) {
    $login = "\nLast login: " . $_COOKIE["last_login_2"];
    setcookie("last_login_1", $_COOKIE["last_login_2"], 0, "/");
    if (!isset($_COOKIE["last_login_1"])) {
        setcookie("last_login_1", date("D M n H:i:s e Y") . " from " . $_SERVER['REMOTE_ADDR'], time() + 30758400, "/");
    }
    setcookie("session_1", "1", 0, "/");
} else {
    $login = "";
    setcookie("last_login_1", date("D M n H:i:s e Y") . " from " . $_SERVER['REMOTE_ADDR'], time() + 30758400, "/");
    setcookie("session_1", "1", 0, "/");
}

define("START_TEXT", "Welcome to 0xhtml (The Bashbin)

 * Webpage:    http://0xhtml.ddns.net
 * Source:     https://github.com/0xhtml/bashbin

  System information as of " . date("D M n H:i:s e Y") . "

  System load:  14.2                Processes:         84
  Usage of /:   18.7% of 218.57GB   Users logged in:   0

 * Here you can store your bash commands and share them
     via a link to all of your friends.
" . $login);

require_once "classes/Commands.php";

$db = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

$commands = new Commands($db);

if (isset($_POST["bash"])) {
    $userCommands = $_POST["bash"];
    $userCommands = json_decode($_POST["bash"]);
    if (json_last_error() == JSON_ERROR_NONE and count($userCommands) > 0) {
        if ($commands->save($userCommands)) {
            header("Location: ?" . $commands->getToken());
            die();
        }
    }
} else {
    if (isset($_SERVER["QUERY_STRING"])) {
        $userToken = $_SERVER["QUERY_STRING"];
        $userToken = trim($userToken);
        if (strlen($userToken) == 10) {
            if (!$commands->load($userToken)) {
                die();
            }
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
<div id="toolbar">
    <button onclick="bash.send()">Save</button>
</div>
<div id="bash"><?= htmlspecialchars(START_TEXT) ?></div>
<script src="js/script.js"></script>
<script>
    let bash;
    window.addEventListener('load', function () {
        bash = new Bash(document.getElementById("bash"), <?= $commands->getCommandsJSArray() ?>, "<?= str_replace("\n", "\\n", htmlspecialchars(START_TEXT)) ?>");
    });
</script>
</body>
</html>
