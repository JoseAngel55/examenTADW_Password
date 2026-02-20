<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/PasswordGenerator.php';
require_once __DIR__ . '/../../models/PasswordLog.php';

class PasswordResource
{
    private $db;
    private $generator;
    private $log;

    public function __construct()
    {
        $database        = new Database();
        $this->db        = $database->getConnection();
        $this->generator = new PasswordGenerator();
        $this->log       = new PasswordLog($this->db);
    }

    // GET /api/v1/password
    public function generate()
    {
        header("Content-Type: application/json");

        $length   = (int) ($_GET['length'] ?? 16);
        $opts     = $this->getOpts($_GET);
        $password = $this->generator->generate($length, $opts);

        $this->log->save([$password]);

        http_response_code(201);
        echo json_encode(array("password" => $password));
    }

    // POST /api/v1/passwords
    public function generateMultiple()
    {
        header("Content-Type: application/json");

        $data      = json_decode(file_get_contents("php://input"));
        $count     = (int) ($data->count  ?? 5);
        $length    = (int) ($data->length ?? 16);
        $opts      = $this->getOpts((array) $data);
        $passwords = $this->generator->generateMultiple($count, $length, $opts);

        $this->log->save($passwords);

        http_response_code(201);
        echo json_encode(array("passwords" => $passwords));
    }

    private function getOpts(array $source)
    {
        return [
            'upper'           => filter_var($source['includeUppercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'lower'           => filter_var($source['includeLowercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'digits'          => filter_var($source['includeNumbers']   ?? true,  FILTER_VALIDATE_BOOLEAN),
            'symbols'         => filter_var($source['includeSymbols']   ?? false, FILTER_VALIDATE_BOOLEAN),
            'avoid_ambiguous' => filter_var($source['excludeAmbiguous'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }
}