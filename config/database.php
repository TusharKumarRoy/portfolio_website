<?php

/* Database configuration */

define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_database');
define('DB_USER', 'tusharkumarroy');
define('DB_PASS', 'oparthib_');

// pdo = php data object
// dsn = data source name

function getDBConnection()
{
    static $pdo = null;

    if($pdo === null)
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new PDO($dsn , DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}


function executeQuery($sql, $params = [])
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
        
    } catch (PDOException $e) {
        error_log("Query Failed: " . $e->getMessage());
        return false;
    }

}


function fetchRow($sql, $params = [])
{
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}


function fetchAll($sql , $params = [])
{
    $stmt = executeQuery($sql , $params);
    return $stmt ? $stmt->fetchAll() : false;
}

?>