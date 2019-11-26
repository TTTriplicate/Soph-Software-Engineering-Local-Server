<?php
 
class DB_CONNECT {

	var $con;
	var $db;
 
    function __construct() {
        // connecting to database
        $this->connect();
    }
 
    function __destruct() {
        // closing db connection
        mysqli_close($this->con);
    }
 
    function connect() {
        // import database connection variables
        require_once __DIR__ . '/db_config.php';
 
        $this->con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysqli_error());
 
        $this->db = mysqli_select_db($this->con, DB_DATABASE) or die(mysqli_error()) or die(mysqli_error());
 
        return $this->con;
    }
 
    function close() {
        // closing db connection
        mysqli_close($this->con);
    }
 
}
 
?>