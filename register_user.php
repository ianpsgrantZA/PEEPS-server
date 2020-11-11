<?php

/*
 * Adds a new user to the DB.
 */

// Return array as JSON
$response = array();

//json data to array
$content = file_get_contents("php://input");
$decoded = json_decode($content, true);
// $username = $decoded['username'];
// echo json_encode($decoded);

//define directory
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/peeps-server');


//check for all required fields
if (isset($decoded['username']) && isset($decoded['password'])) {
    
    $username = $decoded['username'];
    $password = $decoded['password'];

    //include db_connect.php
    // require_once __DIR__ . '/db_connect.php';
    
    // require_once ROOT_PATH . "/db_connect.php";
    // include_once 'db_connect.php';

    //connect to DB
    // $db_con = new DB_CONNECT();
    $db_con = pg_connect("host=localhost port=5432 dbname=PEEPS user=postgres password=admin");

    //insert a new query
    $add_query = "INSERT INTO public.users(username, password) VALUES ('$username', '$password');";
    $result = pg_query($db_con, $add_query);

    //check for errors
    if ($result) {
        //SUCCESS
        $response["success"] = 1;
        $response["message"] = "User registration successful!";

        //echo response
        pg_close();
        echo json_encode($response);
    } else {
        // FAILURE
        $response["success"] = 0;
        $response["message"] = "User registration unsuccessful.";

        //echo response
        pg_close();
        echo json_encode($response);
    }
} else {
    // missing field
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing.";
    // $response["user"] = $decoded['username'];
    // $response["pass"] = $decoded['password'];

    //echo response
    echo json_encode($response);
}


?>