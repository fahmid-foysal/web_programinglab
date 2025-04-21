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

$club_id = isset($_GET["club_id"]) ? (int)$_GET["club_id"] : 0;
$user_id = $_SESSION['user_id'];

if ($club_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid club ID."]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.name, 
            c.img_path, 
            c.moto, 
            c.advisor, 
            (SELECT COUNT(*) FROM user_club WHERE club_id = c.id) AS total_member,
            EXISTS (
                SELECT 1 FROM user_club WHERE user_id = ? AND club_id = c.id
            ) AS is_joined
        FROM clubs c
        WHERE c.id = ?
    ");

    $stmt->execute([$user_id, $club_id]);
    $club = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($club) {
        echo json_encode([
            "status" => "success",
            "message" => "Club fetched successfully",
            "club" => $club,
            "response_code" => $club['is_joined'] ? 1 : 0
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Club not found"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
