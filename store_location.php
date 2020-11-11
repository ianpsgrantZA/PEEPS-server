<?php

/*
 * Stores user location data to table
 */

// Return array as JSON
$response = array();

//json data to array
$content = file_get_contents("php://input");
$decoded = json_decode($content, true);

//define directory
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/peeps-server');

//check for all required fields
if (isset($decoded['user_id']) && isset($decoded['location_lat']) && isset($decoded['location_lon']) && isset($decoded['timestamp'])) {
    
    $user_id = $decoded['user_id'];
    $lat = $decoded['location_lat'];
    $lon = $decoded['location_lon'];
    $timestamp= $decoded['timestamp'];

    //connect to DB
    $db_con = pg_connect("host=localhost port=5432 dbname=PEEPS user=postgres password=admin");

    //check if record exists
    $add_query = "INSERT INTO public.location_data(user_id, timestamp, coordinates) VALUES ('$user_id', '$timestamp',ST_MakePoint($lon,$lat));";
    
    $result = pg_query($db_con, $add_query);

    //check for errors
    if ($result) {
        //SUCCESS
        $response["success"] = 1;
        $response["message"] = "Location updated.";

        //echo response
        pg_close();
        echo json_encode($response);
    } else {
        // FAILURE
        $response["success"] = 0;
        $response["message"] = "Location update failed.";

        //echo response
        pg_close();
        echo json_encode($response);
    }
} else {
    // missing field
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing.";

    //echo response
    echo json_encode($response);
}


?>