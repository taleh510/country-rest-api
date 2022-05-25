<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') :
    http_response_code(405);
    echo json_encode([
        'netice' => 0,
        'message' => 'Səhv sorğu metodu. HTTP metodu GET olmalıdır',
    ]);
    exit;
endif;

require 'database.php';
$database = new Database();
$conn = $database->dbConnection();
$id = null;

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 'cities',
            'min_range' => 1
        ]
    ]);
}

try {
    $sql = is_numeric($id) ? "SELECT * FROM `cities` WHERE id='$id'" : "SELECT * FROM `cities`";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) :

        $data = null;
        if (is_numeric($id)) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'netice' => '1',
            'data' => $data,
        ]);

    else :
        echo json_encode([
            'netice' => '0',
            'message' => 'Netice tapilmadi!',
        ]);
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'netice' => '0',
        'message' => $e->getMessage()
    ]);
    exit;
}