<?php
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    include 'database.php';
    include '../class/DbTest.php';

    $database = new Database();
    $db = $database->getConnection();

    $test = new DbTest($db);

    $response = new DbTest($db);
    echo json_encode($response);

?>