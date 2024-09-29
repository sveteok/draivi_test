<?php
// include_once("db_products.php");
include_once("sqlite_db_products.php");

try {
    $contentType = trim($_SERVER["CONTENT_TYPE"] ?? ''); 

    if ($contentType !== "application/json") {
        throw new Exception('Content type must be: application/json');
    }

    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);

    if(!is_array($decoded) || !isset($decoded["action"])) {
        throw new Exception('Received content contained invalid JSON!');
    }
    
    $result = ['message' => 'Received query is improperly formatted', 'error' => true];
    $action = $decoded["action"];

    switch ($action) {
        case "LOAD_DATA":
            $result = loadProducts();
            break;
        case "ADD_AMOUNT":
            if (isset($decoded["number"])) {
                $number = htmlspecialchars($decoded["number"]);
                updateOrderAmount($number);
                $result = getOrderAmount($number);
            }
            break;
        case "CLEAR_ORDER":
            if (isset($decoded["number"])) {
                $number = htmlspecialchars($decoded["number"]);
                clearOrderAmount($number);
                $result = getOrderAmount($number);
            }
            break;
        case "REMOVE_PRODUCTS_TABLE":
            removeProductsTable();
            $result = ['message' => 'ok', 'error' => false]; 
            break;  
    }

} catch (Exception $e) {
    $result = ['message' => 'Error: ' . $e->getMessage(), 'error' => true];
}

echo json_encode($result);

?>
