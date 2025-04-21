<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
        exit;
    }

    $requiredFields = ["name", "email", "password"];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            echo json_encode(["status" => "error", "message" => "Missing or empty field: $field"]);
            exit;
        }
    }

    $name = trim($input["name"]);
    $email = filter_var($input["email"], FILTER_VALIDATE_EMAIL);
    $password = trim($input["password"]);
    $phone = trim($input["phone"]);


    try {
        $pdo->beginTransaction();

        
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();
                if ($existingUser) {
                echo json_encode(["status" => "error", "message" => "User already exists! please login"]);
                exit();
                  }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $phone]);

    
        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Registration failed", "error" => $e->getMessage()]);
    }
}else{
        echo json_encode(["status" => "error", "message" => "Invalid method! Use POST"]);
    exit();
}
?>