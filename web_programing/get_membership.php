<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db_config.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in as user."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Use GET."]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("
        SELECT m.id , c.name, c.img_path
        FROM user_club m 
        INNER JOIN clubs c ON c.id = m.club_id
        WHERE m.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "joined_clubs" => $interviews]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
