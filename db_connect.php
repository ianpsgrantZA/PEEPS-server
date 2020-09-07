<?php
echo "Connecting Android to Postgres";



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
        // require_once __DIR . '/db_config.php';

        //connect to prostgresql
        //variables
        $server = 'localhost';
        $db_username = 'postgres';
        $db_password = 'admin';
        $db_name = 'PEEPS';

        $db_con = pg_connect("host=$server port=5432 dbname=$db_name user=$db_username password=$db_password");

        return $db_con;
    }

    //close DB
    function close(){
        pg_close();
    }

}



?>