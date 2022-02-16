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
    public function exampleSearchUsingSphinxQL() {

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
}