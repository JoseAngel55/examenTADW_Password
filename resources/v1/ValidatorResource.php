<?php

require_once __DIR__ . '/../../models/PasswordValidator.php';

class ValidatorResource
{
    private $validator;

    public function __construct()
    {
        $this->validator = new PasswordValidator();
    }

    // POST /api/v1/password/validate
    public function validate()
    {
        header("Content-Type: application/json");

        $data     = json_decode(file_get_contents("php://input"));
        $password = $data->password ?? null;

        if (!$password) {
            http_response_code(400);
            echo json_encode(array("message" => "El campo password es requerido."));
            return;
        }

        $requirements = (array) ($data->requirements ?? []);
        $result       = $this->validator->validate($password, $requirements);

        http_response_code($result['valid'] ? 200 : 422);
        echo json_encode($result);
    }
}