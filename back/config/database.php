<?php

    $host = 'localhost'; 
    $dbname = 'sales';
    $user = 'postgres';
    $password = '1234';

    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
        return $pdo;
    } catch (PDOException $e) {

        echo json_encode(
            [
                "sucess" => false,
                "mesage" => "Error: ".$e->getMessage()
            ]
        );

    }

?>
