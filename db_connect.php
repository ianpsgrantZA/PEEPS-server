<?php
echo "Connecting Android to Postgres";

//variables
$server = 'localhost';
$username = 'postgres';
$password = 'admin';
$db_name = 'PEEPS';

// INSERT INTO public.entries(entry_id, user_id, date, "time", nearby, coords) VALUES (?, ?, ?, ?, ?, ?);

class DB_CONNECT{

    //constructor
    function __construct(){
        $this->connect();
    }

    //destructor
    function __destruct(){
        $this.close();
    }

    //connect to database
    function connect(){
        //import DB connection variables
        require_once __DIR . '/db_config.php';

        //connect to prostgresql
        $dbcom = pg_connect("host=$server port=5432 dbname=$db_name user=$username password=$password");

        return $dbcon;
    }

    //close DB
    function close(){
        pg_close();
    }

}



?>