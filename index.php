<?php
require_once "config.php";
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
    <title>The Bashbin</title>
    <meta name="description"
          content="With Bashbin you can store your bash commands and share them via a link with your friends.">
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
        bash = new Bash(
            document.getElementById("bash"),
            <?= $commands->getCommandsJSArray() ?>,
            "<?= str_replace("\n", "\\n", htmlspecialchars(START_TEXT)) ?>",
            "<?= str_replace("\n", "\\n", htmlspecialchars(LINE_START)) ?>"
        );
    });
</script>
</body>
</html>
