<?php
// required headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// database connection will be here
include_once '../config/database.php';
include_once '../objects/product.php';

// instantiate database and product object
$database = new Database();
$db       = $database->getConnection();

// initialize object
$product = new Product($db);

// query products
$stmt = $product->read();
$num  = $stmt->rowCount();

// check if more than 0 record found
if ($num > 0) {

    $products_arr            = [];
    $products_arr['records'] = [];

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $product_item = [
            'id'            => $id,
            'name'          => $name,
            'description'   => html_entity_decode($description),
            'price'         => $price,
            'category_id'   => $category_id,
            'category_name' => $category_name
        ];

        array_push($products_arr['records'], $product_item);

    }

    // set response code - 200 ok
    http_response_code(200);

    // show products data in json format
    echo json_encode($products_arr);

} else {

    // set response code - 404 not fund
    http_response_code(404);

    // tell the user no products found
    echo json_encode([
        'message' => 'No products found.'
    ]);

}
