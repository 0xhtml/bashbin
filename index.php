<?php
require_once "config.php";
require_once "classes/Commands.php";

$commands = new Commands();

if (isset($_POST["bash"])) {
    $userCommands = $_POST["bash"];
    $userCommands = json_decode($_POST["bash"]);
    if (json_last_error() == JSON_ERROR_NONE and count($userCommands) > 0) {
        $db = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if ($commands->save($db, $userCommands)) {
            header("Location: ?" . $commands->getToken());
            die();
        }
    }
} else {
    if (isset($_SERVER["QUERY_STRING"])) {
        $userToken = $_SERVER["QUERY_STRING"];
        $userToken = trim($userToken);
        if (strlen($userToken) == 10) {
            $db = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
            if (!$commands->load($db, $userToken)) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bashbin - The Pastebin for bash commands</title>
    <link rel="shortcut icon" href="favicon.ico">
    <meta name="description"
          content="With Bashbin you can store your bash commands and share them via a link with your friends.">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>
<body>
<div id="toolbar">
    <button onclick="bash.send()">Save</button>
</div>
<div id="bash">
    <h1 id="headline">The Bashbin</h1>

    <img src="favicon.ico" alt="Via Bashbin you can easily share bash commands." title="Welcome to Bashbin.">

    <p id="links">
        Webpage:&nbsp; <a href="https://bashbin.000webhostapp.com">https://bashbin.000webhostapp.com</a><br>
        Source:&nbsp;&nbsp; <a href="https://github.com/0xhtml/bashbin">https://github.com/0xhtml/bashbin</a>
    </p>

    <p id="sysinfo">
        &nbsp;&nbsp;System&nbsp;load:&nbsp;&nbsp;14.2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Processes:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;84<br>
        &nbsp;&nbsp;Usage&nbsp;of&nbsp;/:&nbsp;&nbsp;&nbsp;18.6%&nbsp;of&nbsp;1.5TB &nbsp;&nbsp;Users&nbsp;logged&nbsp;in:&nbsp;7&nbsp;<br>
        &nbsp;&nbsp;Memory&nbsp;usage:&nbsp;5%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Swap&nbsp;usage:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0%
    </p>

    <p class="motd">Here you can store your bash commands and share them via a link to all of your friends.</p>
    <p class="motd">We are basically a Pastebin were you can store your bash commands.</p>

    <div id="input"></div>
</div>
<script>
    let bash = new Bash(
        document.getElementById("input"),
        <?= $commands->getCommandsJSArray() ?>,
        "<?= str_replace("\n", "\\n", LINE_START) ?>"
    );
</script>
</body>
</html>
