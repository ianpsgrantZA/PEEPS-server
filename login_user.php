<?php

/*
 * Checks if user has an account
 */

// Return array as JSON
$response = array();

//json data to array
$content = file_get_contents("php://input");
$decoded = json_decode($content, true);

//define directory
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/peeps-server');

//check for all required fields
if (isset($decoded['username']) && isset($decoded['password'])) {
    
    $username = $decoded['username'];
    $password = $decoded['password'];

    //connect to DB
    $db_con = pg_connect("host=localhost port=5432 dbname=PEEPS user=postgres password=admin");

    //check if record exists
    $add_query = "SELECT COUNT(1) FROM public.users WHERE username = '$username' AND password = '$password';";
    
    $result = pg_query($db_con, $add_query);

    //check for errors
    if ($result) {
        //SUCCESS
        $response["success"] = 1;
        $response["message"] = "Accessed database successfully";

        $row = pg_fetch_assoc($result);
        if ($row["count"]) {
            $response["login_status"] = 1;
        }else {
            $response["login_status"] = 0;
        }

        //echo response
        pg_close();
        echo json_encode($response);
    } else {
        // FAILURE
        $response["success"] = 0;
        $response["message"] = "User login unsuccessful: unable to query database.";

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