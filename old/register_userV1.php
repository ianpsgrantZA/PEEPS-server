<?php

/*
 * Adds a new user to the DB.
 */

// Return array as JSON
$response = array();

//check for all required fields
if (isset($_POST['username']) && isset($_POST['password'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    //include db_connect.php
    require_once __DIR__ . '/db_connect.php';

    //connect to DB
    $db_con = new DB_CONNECT();

    //insert a new query
    $add_query = "INSERT INTO public.users(username, password) VALUES ($username, $password);";
    $result = pg_query($db_con, $add_query);

    //check for errors
    if ($result) {
        //SUCCESS
        $response["success"] = 1;
        $response["message"] = "User registration successful!";

        //echo response
        echo json_encode($response);
    } else {
        // FAILURE
        $response["success"] = 0;
        $response["message"] = "User registration unsuccessful.";

        //echo response
        echo json_encode($response);
    }
} else {
    // missing field
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing.";
    $response["user"] = $_POST['username'];
    $response["pass"] = $_POST['password'];

    //echo response
    echo json_encode($response);
}


?>