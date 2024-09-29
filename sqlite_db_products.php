<?php
include_once("config.php");

function createProductsTable() {
    global $db;
    try {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            number TEXT NOT NULL,
            name TEXT NOT NULL,
            bottlesize TEXT,
            price REAL NOT NULL,
            priceGBP REAL NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            orderamount INTEGER NOT NULL DEFAULT 0,
            UNIQUE(number) ON CONFLICT REPLACE
        );
        CREATE UNIQUE INDEX IF NOT EXISTS products_number_idx ON products(number);";

        $db->exec($createTableQuery);
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage(); 
    }
}

function insertProduct($data) {
    global $db;

    $insertQuery = $db->prepare("INSERT INTO products (number, name, bottlesize, price, priceGBP) 
                                    VALUES (:number, :name, :bottlesize, :price, :priceGBP)
                                    ON CONFLICT(number) DO UPDATE SET
                                    name=:updated_name, bottlesize=:updated_bottlesize, price=:updated_price, priceGBP=:updated_priceGBP, timestamp=CURRENT_TIMESTAMP;");

    $insertQuery->execute([
        ':number' => $data['number'],
        ':name' => $data['name'],
        ':bottlesize' =>  $data['bottlesize'] || '',
        ':price' => $data['price'] ,
        ':priceGBP' => $data['priceGBP'],
        ':updated_name' => $data['name'],
        ':updated_bottlesize' => $data['bottlesize'],
        ':updated_price' => $data['price'],
        ':updated_priceGBP' => $data['priceGBP']
    ]);
}

function loadProducts() {
    global $db;
    $result = $db->query("SELECT * FROM products ORDER BY id");
    $data = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        array_push($data, $row);
    }
    return $data;
}

function removeProductsTable() {
    global $db;
    $stmt = $db->prepare("DROP TABLE IF EXISTS products");
    $stmt -> execute();
}

function updateOrderAmount($number) {
    global $db;
    $stmt = $db->prepare("UPDATE products SET orderamount = orderamount + 1 WHERE number = :number");
    $stmt->execute([':number' => $number]);
}

function clearOrderAmount($number) {
    global $db;
    $stmt = $db->prepare("UPDATE products SET orderamount = 0 WHERE number = :number");
    $stmt->execute([':number' => $number]);
}

function getOrderAmount($number) {
    global $db;
    $stmt = $db->prepare("SELECT number, orderamount, timestamp FROM products WHERE number = :number");
    $stmt -> bindValue(':number', $number);
    $stmt->execute();

    $result  = $stmt->fetch();
    return $result;
}

?>
