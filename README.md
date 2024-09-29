# Draivi Backend Test 9/202

## Description

This project consists of two parts:

- **Part 1** Fetch a price list from an external site, parse the data, and store it in a database. The script can be rerun to update the existing data with the latest values.
- **Part 2** Fetch the stored data from the database, display it on the front-end, and allow modification of specific values (e.g., order amount). The modified values are saved back to the database without page reload.

The project includes a complete environment setup, configuration instructions, and necessary database and front-end components.

## Project Structure

- **.env**: Stores configuration settings (e.g., API keys, database credentials) - not in git
- **.env.example**: Example template for the .env file.
- **config.php**: Database connection configuration
- **data_loader.php**: Fetches the price list, parses it, and stores it in the database

  Example usage:
  ```sh
  > php data_loader.php
  Downloading the file and storing it into local temporary file... 
  Loading the excel file into PhpSpreadsheet... 
  Creating products table if it doesn't exist... 
  Iterating through the worksheet and updating data in the database...
  Clean up temp file...
  Done.
  Processng time: 17.175938129425
  ```
- **db_products.php**: Contains functions for interacting with the MySQL database
- **sqlite_db_products.php**: Contains functions for interacting with the SQLite3 database
- **currency_converter.php**: Currency conversion class to convert prices between different currencies
  
  Example usage:
  ```php
  echo "1 USD = " . CurrencyConverter::convert('USD', 'GBP', 1) . " GBP\n";
  echo "50 USD = " . CurrencyConverter::convert('USD', 'GBP', 50) . " GBP\n";
  echo "17.99 EUR = " . CurrencyConverter::convert('EUR', 'GBP', 17.99) . " GBP\n";
  ```
- **actions.php**: Fetches data from the database and allows value modification via the front-end
- **index.html**: A simple HTML page with buttons to load and manipulate product data:
  - "**List**" button: Fetches and displays the product list from the database in a table.
  - "**Empty**" button: Clears the product table.
    
    Each product has buttons:
  - "**Add**": Increments the order amount for a product.
  - "**Clear**": Resets the order amount to zero.

## Requirements

To test and run this project locally, you will need a local PHP development environment. You can use the built-in PHP server or install a local server like XAMPP/WAMP/MAMP.

### _Local Server Options:_

- PHP Built-in Web Server (Quick, lightweight, minimal setup).
- XAMPP (Cross-platform, full-stack local server environment).
- WAMP (Recommended for Windows users).
- MAMP (Works on macOS and Windows).

### _Software Versions:_

- PHP: Version 8.3.11
- Database: MySQL (Ver 9.0.1) or SQLite (version 3.43.2)
- PHP Extensions: SQLite (default in PHP) or MySQL

### _Required Libraries:_

- PhpSpreadsheet (for reading Excel files): Install via Composer:
  ```sh
  composer require phpoffice/phpspreadsheet
  ```
- PHP dotenv (for managing environment variables): Install via Composer:
  ```sh
  composer require vlucas/phpdotenv
  ```
  composer.json:

```json
"require": {
  "phpoffice/phpspreadsheet": "^2.2",
  "vlucas/phpdotenv": "^5.6"
}
```

### _Database Setup:_

You can use either MySQL or SQLite as your database.

**SQLite Configuration (default):**

- No additional setup required; the PHP SQLite extension is enabled by default.
  - the config.php
  ```php
  // SQLite connection (for SQLite setup):
  $db = new PDO('sqlite:' . $databasename);
  ```
  - Files data_loader.php and actions.php use next include:
  ```php
   //(for SQLite include sqlite_db_products.php):
   include_once("sqlite_db_products.php");
  ```

**MySQL Configuration:**

- Ensure MySQL is installed and running.
- Modify the config.php file to use MySQL:
  - Comment out the SQLite configuration.
  - Uncomment the MySQL connection block.
  ```php
   // MySQL connection (for MySQL setup):
   $dsn = 'mysql:dbname=' . $databasename . ';host=' . $hostname;
   $db = new PDO($dsn, $username, $password);
   $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  ```
- Modify files data_loader.php and actions.php to use MySQL:
  - Comment include db_products.php.
  - Uncomment include sqlite_db_products.php.
  ```php
   //(for MySQL include sqlite_db_products.php):
   include_once("db_products.php");
  ```

### Setup Instructions

1. **Install Dependencies**: Run the following command to install required PHP libraries:
   ```bash
   composer install
   ```
2. **Configure Environment**: Copy .env.example to .env and set up your environment variables:
   ```bash
   cp .env.example .env
   ```
   Modify the .env file:
   ```bash
    CURRENCYLAYER_API_ACCESS_KEY=your_currencylayer_api_access_key
    DATABASE_USERNAME=username
    DATABASE_PASSWORD=password
    DATABASE_HOSTNAME=hostname
    DATABASE_NAME=databasename
    TEMP_FILE=/path/to/temp/file
    XLS_URL=http://example.com/path/to/pricelist.xlsx
   ```
3. **Run the PHP Built-in Server**: For quick local testing, use PHP's built-in web server:
   ```bash
   php -S localhost:8000
   ```
   Visit http://localhost:8000 to view the project.

### Explanation of Part 1

The **data_loader.php** script performs the following actions:

- **Download the Excel File**: Fetches the price list from the provided URL.
- **Read the Excel File**: Uses PhpSpreadsheet to parse the file's contents.
- **Insert Data into the Database**: Inserts the parsed data into the MySQL or SQLite database.
- **Cleanup**: Deletes the temporary file once the data is saved to the database.

Data sources:

1. **Currencylayer API**: Used for real-time currency conversion rates (free tier available at https://currencylayer.com).
2. **Alko Price List**: Daily-updated product list (https://www.alko.fi/valikoimat-ja-hinnasto/hinnasto).

### Explanation of Part 2

The **index.html** file serves as the front-end interface for viewing and updating product data stored in the database. This is achieved through interaction with the server-side **actions.php** API.

The communication between the front-end (HTML) and back-end (PHP) is handled using JavaScript's **Fetch API**, which sends and receives data in **JSON format**. This allows for seamless, asynchronous HTTP requests to **actions.php**, updating the order amount directly in the database and reflecting the changes on the page dynamically without reloading.

### Additional Notes

- Be sure to use the correct API keys and database credentials in your .env file.
- If you're using MySQL, ensure the database is created prior to running the project.
