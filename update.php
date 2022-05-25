<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, country_idization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'netice' => 0,
        'message' => 'Səhv sorğu metodu. HTTP metodu PUT olmalıdır',
    ]);
    exit;
endif;

require 'database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['netice' => 0, 'message' => 'Zəhmət olmasa ID-ni daxil edin.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `cities` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $post_ad = isset($data->ad) ? $data->ad : $row['ad'];
        $post_country_id = isset($data->country_id) ? $data->country_id : $row['country_id'];

        $update_query = "UPDATE `cities` SET  ad = :ad, country_id = :country_id 
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':ad', htmlspecialchars(strip_tags($post_ad)), PDO::PARAM_STR);
        $update_stmt->bindValue(':country_id', htmlspecialchars(strip_tags($post_country_id)), PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'netice' => 1,
                'message' => 'Post uğurla yeniləndi.'
            ]);
            exit;
        }

        echo json_encode([
            'netice' => 0,
            'message' => 'Post yenilənə bilmədi.'
        ]);
        exit;

    else :
        echo json_encode(['netice' => 0, 'message' => 'Yanlış ID.ID tərəfindən heç bir şəhər tapılmadı.']);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'netice' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}