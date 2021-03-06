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
if (isset($decoded['locations']) && isset($decoded['times']) ) {
    
    $locations = $decoded['locations'];
    $times = $decoded['times'];
    $length = count($times);

    //connect to DB
    $db_con = pg_connect("host=localhost port=5432 dbname=PEEPS user=postgres password=admin");

    //query vars
    $week_scope = 3; // 3 weeks
    $current_date = date('Y-m-d');
    $previous_date = date('Y-m-d', strtotime('-' . $week_scope . ' week', strtotime($current_date)));
    $current_date = date('Y-m-d', strtotime('-' . $week_scope . ' day', strtotime($current_date)));
    $i2=8;

    //assume success until failure
    $response["success"] = 1;
    $response["message"] = "Data received.";



    // people in contact with
    $contacts = array();

    //Calculate Day of Week
    $dow = date('w', strtotime($current_date))+1;

    for ($i=8; $i < 23; $i++) { 
        $i2=$i+1; //set end time to be hour after first
        $count = 0;

        

        foreach ($locations as $loc) {
            
            //database query DISTINCT user_id
            $add_query = "SELECT COUNT(user_id) FROM public.location_data" .
            " WHERE timestamp::date BETWEEN '$previous_date' AND '$current_date'" . // date within 3 weeks
            " AND timestamp::time BETWEEN time '$i:00:00' AND time '$i2:00:00'" . //between hour and hour+1
            " AND EXTRACT(DOW FROM timestamp) = " . $dow . // on day of week of current date
            " AND ST_Distance(ST_Transform('SRID=4326;POINT($loc[1] $loc[0])'::geometry, 3857), ST_Transform(ST_SetSRID(coordinates,4326),3857))  <= 20"; //within 20 meters of input coordinates

            // echo $add_query;

            $result = pg_query($db_con, $add_query);

            //check for errors
            if ($result) {
                //SUCCESS
                $row = pg_fetch_assoc($result);
                $response["n".strval($i)."_".strval($i2)."_loc".$count] = $row["count"] + 0 ;

            } else {
                // FAILURE
                $response["success"] = 0;
                $response["message"] = "Data not avaliable.";

                $error = true;
                break;
            }
            $count++;
            
        }

        

    }

    //echo response
    pg_close();
    echo json_encode($response);

    
} else {
    // missing field
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing.";

    //echo response
    echo json_encode($response);
}


?>