<?php

class SphinxModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    public function exampleSearch() {



        $cl = new SphinxClient(); // Will it work?

        
        return "Hello World!";

    }
    /**
     * @method exampleSearch
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function exampleSearchUsingSphinxQLLocal() {

        $wgSphinxSearch_host = '172.31.47.173';
        $wgSphinxSearch_port = 9312; // 9306 is our SphinxQL port - let's us use SQL syntax to query the search engine.

        $mysqli = new mysqli($wgSphinxSearch_host, "user", "password", "database", 9306);


        if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);
        // Perform an initial query to the Sphinx search engine.

        // Configure our default database connection.
        // Database::setDefault();

        // Use this connection to perform a query.


        // Iterate through the query results.



        return "It works!";
    }


    /**
     * @method exampleSearchUsingSphinxQLRemote
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function exampleSearchUsingSphinxQLRemote() {

        $wgSphinxSearch_host = "35.162.222.119"; // Jose will update this IP with the public IP of OCDLA's database / search server.

        $wgSphinxSearch_port = 9312; // 9306 is our SphinxQL port - let's us use SQL syntax to query the search engine.
        // mysql -P9306 -h52.42.123.92 -protocol=tcp --prompt='sphinxQL> '"
        // $mysqli = new Mysqli("52.42.123.92:9306","","","");


        /*
        // PDO OPTION 
        $dsn = 'mysql:host=35.162.222.119;port=9306';

        try {
            $pdo = new PDO($dsn);
            $this->isConnected = true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        */

        $link = mysqli_connect("35.162.222.119", "", "", "", 9306);
        if (!$link) {
            echo "Error: Unable to connect to MySQL." . "<p>";
            echo "Debugging errno: " . mysqli_connect_errno() . "<p>";
            echo "Debugging error: " . mysqli_connect_error() . "<p>";
            exit;
        }
        
  
        // Configure our default database connection.
        // Database::setDefault();

        // Use this connection to perform a query.


        // Iterate through the query results.



        return "It works!";
    }
}