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
    <title>Bashbin - The Pastebin to easily share all your bash commands</title>
    <link rel="shortcut icon" href="favicon.ico">
    <meta name="description"
          content="With Bashbin you can store your bash commands and share &#x2709; them via a link &#x261d; with your friends. A place to copy &#x2702; paste &#x270e; your bash commands.">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>
<body>
<div id="toolbar">
    <button onclick="bash.send()">Save</button>
</div>
<div id="bash">
    <h1 id="headline">The Bashbin v1.0 <img src="favicon.ico" class="hidden" alt="Via Bashbin you can easily share bash commands!" title="Welcome to the Bashbin"></h1>

    <p id="links">
        Webpage:&nbsp; <a href="https://bashbin.000webhostapp.com">https://bashbin.000webhostapp.com</a><br>
        Source:&nbsp;&nbsp; <a href="https://github.com/0xhtml/bashbin">https://github.com/0xhtml/bashbin</a>
    </p>

    <p id="sysinfo">
        &nbsp;&nbsp;System&nbsp;load:&nbsp;&nbsp;14.2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Processes:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;84<br>
        &nbsp;&nbsp;Usage&nbsp;of&nbsp;/:&nbsp;&nbsp;&nbsp;18.6%&nbsp;of&nbsp;1.5TB &nbsp;&nbsp;Users&nbsp;logged&nbsp;in:&nbsp;7&nbsp;<br>
        &nbsp;&nbsp;Memory&nbsp;usage:&nbsp;5%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Swap&nbsp;usage:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0%
    </p>

    <p class="motd">Here you can store your bash commands and share them via a link to all of your friends. If you don't have friends you can also store them for yourself.</p>
    <p class="motd">We are basically a Pastebin were you can store your bash commands. We are a place to copy paste your bash commands to.</p>
    <p class="motd">The easiest way to store your commands after you clicked "Save" is to just bookmark the page. How much more easily can it be?</p>

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
