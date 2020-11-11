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
if (isset($decoded['locations'])) {
    
    $locations = $decoded['locations'];
    $length = count($locations);
    
    // require_once ROOT_PATH . "/db_connect.php";

    //query vars
    $week_scope = 3; // 3 weeks
    $current_date = date('Y-m-d');
    $previous_date = date('Y-m-d', strtotime('-' . $week_scope . ' week', strtotime($current_date))); 
    $current_date = date('Y-m-d', strtotime('-' . $week_scope . ' day', strtotime($current_date))); //uninclude current day's location uploads
    $i2=8;

    //Calculate Day of Week
    $dow = date('w', strtotime($current_date))+1;

    //connect to DB
    $db_con = pg_connect("host=localhost port=5432 dbname=PEEPS user=postgres password=admin");

    //assume success until failure
    $response["success"] = 1;
    $response["message"] = "Data received.";

    for ($i=8; $i < 23; $i++) { //from 8:00 to 23:00
        $i2=$i+1; //set end time to be one hour after first
        $count = 0;

        foreach ($locations as $loc) {

            //database query
            $add_query = "SELECT COUNT(DISTINCT user_id) FROM public.location_data" .
                    " WHERE timestamp::date BETWEEN '$previous_date' AND '$current_date'" . // date within 3 weeks
                    " AND timestamp::time BETWEEN time '$i:00:00' AND time '$i2:00:00'" . //between hour and hour+1
                    " AND EXTRACT(DOW FROM timestamp) = " . $dow . // on day of week of current date
                    " AND ST_Distance(ST_Transform('SRID=4326;POINT($loc[0] $loc[1])'::geometry, 3857), ST_Transform(ST_SetSRID(coordinates,4326),3857))  <= 20"; //within 20 meters of input coordinates

            // echo $add_query;
        
            $result = pg_query($db_con, $add_query);

            //check for errors
            if ($result) {
                //SUCCESS
                $row = pg_fetch_assoc($result);
                // $value = ($row["count"]+0)/$week_scope;
                $response["n".strval($i)."_".strval($i2)."_loc".$count] = $row["count"]+0;

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