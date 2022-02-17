<?php

class SphinxModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    // Main callback; return a call to the SphinxQL method.
    public function exampleSearch() {

        return $this->exampleSearchUsingSphinxQL();
    }


    // If necessary, use the included SphinxApi client library.
    // See ocdladefense/lib-sphinx-search for more info.
    public function exampleSearchSphinxApi() {



        $cl = new SphinxClient(); // Will it work?

        
        return "Using SphinxApi client library!";
    }




    /**
     * @method exampleSearchUsingSphinxQL
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function exampleSearchUsingSphinxQL() {

        // Jose will update this IP with the public IP of OCDLA's database / search server.
        // This is the Elastic IP of the Database Server.
        $sphinxHost = "35.162.222.119"; 

        // This is our SphinxQL port - let's us use SQL syntax to query the search engine.
        $sphinxQLPort = 9306; 

        // Alternate port when using the included SphinxApi.php library.
        $sphinxApiPort = 9312; 

        // Example CLI commands for testing availability of searchd.
        // mysql -P9306 -h35.162.222.119 -protocol=tcp --prompt='sphinxQL> '"
        // $mysqli = new Mysqli("35.162.222.119:9306","","","");


        /*
        // PDO OPTION - We can use the PDO library when preferred.
        $dsn = 'mysql:host=35.162.222.119;port=9306';

        try {
            $pdo = new PDO($dsn);
            $this->isConnected = true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        */

        $link = mysqli_connect($sphinxHost, "", "", "", $sphinxQLPort);
        if (!$link) {
            echo "Error: Unable to connect to MySQL." . "<p>";
            echo "Debugging errno: " . mysqli_connect_errno() . "<p>";
            echo "Debugging error: " . mysqli_connect_error() . "<p>";
            exit;
        }
        
  


        // Use this connection to perform a query.


        // Iterate through the query results.



        return "Using SphinxQL via Mysqli client library!";
    }
}