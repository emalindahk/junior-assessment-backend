<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Product.php';

$database = new Database();
$db = $database->connect();

$product = new Product($db);

$data = json_decode(file_get_contents("php://input"));

$product->sku = $data->sku;
$product->name = $data->name;
$product->price = $data->price;
$product->category_id = $data->category_id;
$product->attribute_value = $data->attribute_value;



if ($product->create()) {
    echo json_encode(
        array(
            'message' => 'Product Created',
            'success' => true
        )
    );
} else {
    echo json_encode(
        array('message' => 'Product Not Created')
    );
}
