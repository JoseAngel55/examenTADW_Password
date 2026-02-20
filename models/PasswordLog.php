<?php

class PasswordLog
{
    private $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    public function save(array $passwords): void
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO generated_passwords (password, length) VALUES (?, ?)"
        );

        foreach ($passwords as $pw) {
            $stmt->execute([$pw, mb_strlen($pw)]);
        }
    }
}