<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../config/Database.php';
    include_once '../../models/Product.php';

    $database = new Database();
    $db = $database->connect();

    $product = new Product($db);

    $results = $product->getAll();
    $num = $results->rowCount();

    if($num > 0) {
        $products_arr = array();
        $products_arr['data'] = array();

        while($row = $results->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $product_item = array(
                'id' => $id,
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'category_name' => $category_name,
                'attribute_name' => $attribute_name,
                'attribute_value' => $value
            );

            array_push($products_arr['data'], $product_item);
        }
        echo json_encode($products_arr);

    } else {
        echo json_encode(
            array('message' => 'No Products Found')
        );
    }

?>
