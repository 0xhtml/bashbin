<?php

class Commands
{
    private $db;
    private $token = "";
    private $commands = "";

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function load(string $token)
    {
        $statement = $this->db->prepare("SELECT * FROM commands WHERE token = ?");
        $statement->bind_param("s", $token);

        if (!$statement->execute()) {
            return false;
        }

        $result = $statement->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $this->commands = $result->fetch_object()->commands;
        $this->token = $token;

        return true;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function save(string $commands)
    {
        if ($this->commands == $commands) {
            return false;
        }

        $this->commands = $commands;

        $statement = $this->db->prepare("SELECT * FROM commands WHERE commands = ?");
        $statement->bind_param("s", $this->commands);

        if (!$statement->execute()) {
            return false;
        }

        $result = $statement->get_result();

        if ($result->num_rows >= 1) {
            $result = $result->fetch_object();
            $this->token = $result->token;
            return true;
        }

        $this->genToken();

        $statement = $this->db->prepare("INSERT INTO commands (token, commands) VALUES (?, ?)");
        $statement->bind_param("ss", $this->token, $this->commands);

        if (!$statement->execute()) {
            return false;
        }

        return true;
    }

    private function genToken()
    {
        while (true) {
            $token = substr(sha1(uniqid()), 0, 10);
            $statement = $this->db->prepare("SELECT * FROM commands WHERE token = ?");
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
