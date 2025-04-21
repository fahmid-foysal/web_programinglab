<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start(); 
require_once 'db_config.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as user."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
    exit;
}

$json_data = file_get_contents("php://input");
$input = json_decode($json_data, true);

if ($json_data === false || is_null($input)) {
    echo json_encode(["status" => "error", "message" => "Invalid or empty JSON request."]);
    exit;
}

if (!isset($input['club_id'])) {
    echo json_encode(["status" => "error", "message" => "club_id is required."]);
    exit;
}

$user_id = $_SESSION["user_id"];
$club_id = $input['club_id'];

try {
    $stmt = $pdo->prepare("
        INSERT INTO user_club (user_id, club_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$user_id, $club_id]);

    echo json_encode(["status" => "success", "message" => "Joined successfully."]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
