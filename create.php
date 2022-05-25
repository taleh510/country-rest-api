<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'netice' => 0,
        'message' => 'Səhv sorğu metodu. HTTP metodu POST olmalıdır',
    ]);
    exit;
endif;

require 'database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->ad)) :

    echo json_encode([
        'netice' => 0,
        'message' => 'Zəhmət olmasa bütün sahələri doldurun | ad.',
    ]);
    exit;

elseif (empty(trim($data->ad)) || empty(trim($data->country_id))) :

    echo json_encode([
        'netice' => 0,
        'message' => 'Zəhmət olmasa bütün sahələri doldurun.',
    ]);
    exit;

endif;

try {

    $ad = htmlspecialchars(trim($data->ad));
    $country_id = htmlspecialchars(trim($data->country_id));


    $query = "INSERT INTO `cities`(ad,country_id) VALUES(:ad,:country_id)";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':ad', $ad, PDO::PARAM_STR);
    $stmt->bindValue(':country_id', $country_id, PDO::PARAM_INT);

    if ($stmt->execute()) {

        http_response_code(201);
        echo json_encode([
            'netice' => 1,
            'message' => 'Məlumat uğurla daxil edildi.'
        ]);
        exit;
    }
    echo json_encode([
        'netice' => 0,
        'message' => 'Məlumat daxil edilmədi.'
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'netice' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}