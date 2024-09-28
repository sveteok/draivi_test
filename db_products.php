<?php
include_once("config.php");

function createProductsTable() {
    global $db;
    $createTableQuery = "CREATE TABLE IF NOT EXISTS products (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        number VARCHAR(20) UNIQUE NOT NULL,
        name VARCHAR(255) NOT NULL,
        bottlesize VARCHAR(20),
        price DECIMAL(7,2) NOT NULL,
        priceGBP DECIMAL(7,2)  NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        orderamount INT(10) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $db->exec($createTableQuery);
}

function insertProduct($data) {
    global $db;

    $insertQuery = $db->prepare("INSERT INTO products (number, name, bottlesize, price, priceGBP) 
                                    VALUES (:number, :name, :bottlesize, :price, :priceGBP) 
                                    ON DUPLICATE KEY 
                                    UPDATE name=:updated_name, bottlesize=:updated_bottlesize, price=:updated_price, priceGBP=:updated_priceGBP, timestamp=CURRENT_TIMESTAMP;");

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
?>