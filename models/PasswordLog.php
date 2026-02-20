<?php

class PasswordLog
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function save(array $passwords)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO generated_passwords (password, length) VALUES (?, ?)"
        );

        foreach ($passwords as $pw) {
            $stmt->execute([$pw, mb_strlen($pw)]);
        }
    }
}