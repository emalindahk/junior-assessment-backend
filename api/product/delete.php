<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$db = $database->connect();

$product = new Product($db);

$data = json_decode(file_get_contents("php://input"));

// $product->ids = array_map('intval', explode(",", $data->ids));
$product->ids = explode(",", $data->ids);

if ($product->delete()) {
    echo json_encode(array(
        'message' => 'Products Deleted',
        'success' => true
    ));
} else {
    echo json_encode(array('message' => 'Products Not Deleted'));
}
