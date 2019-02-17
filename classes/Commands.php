<?php

class Commands
{
    private $token = "";
    private $commands = [""];

    public function load(mysqli $db, string $token)
    {
        $statement = $db->prepare("SELECT * FROM saves WHERE token = ?");
        $statement->bind_param("s", $token);

        if (!$statement->execute()) {
            return false;
        }

        $result = $statement->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $result = $result->fetch_assoc();
        $this->commands = json_decode($result["commands"]);
        $this->token = $token;

        return true;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function getCommandsJSArray()
    {
        $JSCommands = "[";
        foreach ($this->commands as $key => $value) {
            $JSCommands .= "\"";
            $JSCommands .= str_replace("\"", "\\\"", htmlspecialchars($value));
            $JSCommands .= "\",";
        }
        $JSCommands .= "]";
        return $JSCommands;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function save(mysqli $db, array $commands)
    {
        if ($this->commands == $commands) {
            return false;
        }

        $this->commands = $commands;
        $json_commands = json_encode($this->commands);

        $statement = $db->prepare("SELECT * FROM saves WHERE commands = ?");
        $statement->bind_param("s", $json_commands);

        if (!$statement->execute()) {
            return false;
        }

        $result = $statement->get_result();

        if ($result->num_rows >= 1) {
            $result = $result->fetch_object();
            $this->token = $result->token;
            return true;
        }

        $this->genToken($db);

        $statement = $db->prepare("INSERT INTO saves (token, commands) VALUES (?, ?)");
        $statement->bind_param("ss", $this->token, $json_commands);

        if (!$statement->execute()) {
            return false;
        }

        return true;
    }

    private function genToken(mysqli $db)
    {
        while (true) {
            $token = substr(sha1(uniqid()), 0, 10);
            $statement = $db->prepare("SELECT * FROM saves WHERE token = ?");
            $statement->bind_param("s", $token);

            if (!$statement->execute()) {
                return;
            }

            if ($statement->get_result()->num_rows === 0) {
                $this->token = $token;
                break;
            }
        }
    }

}
