<?php

require __DIR__ . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

include_once("currency_converter.php");
include_once("db_products.php");

function loadDataToDB() {

    try {
        ini_set('memory_limit', '512M'); 

        $tempFile = $_ENV["TEMP_FILE"]; 
        $fileUrl = $_ENV["XLS_URL"];
        
        echo "Downloading the file and storing it into local temporary file... " . PHP_EOL;
        file_put_contents($tempFile, file_get_contents($fileUrl));

        echo "Loading the excel file into PhpSpreadsheet... " . PHP_EOL;
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        
        $spreadsheet = $reader->load($tempFile);
        $worksheet = $spreadsheet->getActiveSheet();

        echo "Creating products table if it doesn't exist... " . PHP_EOL;
        createProductsTable();

        echo "Iterating through the worksheet and updating data in the database..." . PHP_EOL;
        $highestRow = $worksheet->getHighestDataRow();
        for ($row = 5; $row <= $highestRow; ++$row) {
            $price = $worksheet->getCell([5, $row])->getValue();
            $data = array(
                'number' => $worksheet->getCell([1, $row])->getValue(),
                'name' => $worksheet->getCell([2, $row])->getValue(),
                'bottlesize' => $worksheet->getCell([4, $row])->getValue(),
                'price' => $price,
                'priceGBP' => CurrencyConverter::convert('EUR', 'GBP', $price)
            );
            insertProduct($data);
        }

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    } finally {
        echo "Clean up temp file..." . PHP_EOL;
        unlink($tempFile);
        echo "Done." . PHP_EOL;
    }
}

$startTime = microtime(true);  
loadDataToDB();
echo "Processng time: ". (microtime(true) - $startTime) . PHP_EOL;

?>