<?php




class SphinxModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    // Main callback; return a call to the SphinxQL method.
    public function exampleSearch($terms) {

        return $this->exampleSearchUsingSphinxQL($terms);
    }


    // If necessary, use the included SphinxApi client library.
    // See ocdladefense/lib-sphinx-search for more info.
    public function exampleSearchSphinxApi($terms) {



        $cl = new SphinxClient(); // Will it work?

        
        return "Using SphinxApi client library!";
    }




    /**
     * @method exampleSearchUsingSphinxQL
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function exampleSearchUsingSphinxQL($terms) {

        // If we're experimenting then let's not bother returning the theme.
        $debug = true;

        // Jose will update this IP with the public IP of OCDLA's database / search server.
        // This is the Elastic IP of the Database Server.
        $sphinxHost = "35.162.222.119"; 

        // This is our SphinxQL port - let's use SQL syntax to query the search engine.
        $sphinxQLPort = 9306; 

        // Alternate port when using the included SphinxApi.php library.
        $sphinxApiPort = 9312; 

        // We'll perform a secondary $api query for the actual Salesforce 
        // products using these IDs.
        $productIds = array();


        // $terms = "duii";


        $api = $this->loadForceApi();


        $results_html = array(); 

        $domain = "https://ocdla.force.com";

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

        $conn = mysqli_connect($sphinxHost, "", "", "", $sphinxQLPort);
        if (!$conn) {
            echo "Error: Unable to connect to MySQL." . "<p>";
            echo "Debugging errno: " . mysqli_connect_errno() . "<p>";
            echo "Debugging error: " . mysqli_connect_error() . "<p>";
            exit;
        }
        
        
        $ql = "SELECT * FROM ocdla_products WHERE MATCH('%s')";

  
        $query = sprintf($ql,$terms);

        $result = mysqli_query($conn, $query);

        // Iterate through the query results.
        while($row = mysqli_fetch_assoc($result)) {
            $productIds[] = $row["product_id"];
            //var_dump($row);
        }

        if(count($productIds) < 1) {
            return "No results found.";
        }

        // Our list of proudct IDs.


        $fn = function($id){return "'{$id}'";};

        $step1 = array_map($fn, $productIds);


        $step2 = implode(",", $step1);


        // Per usual.
        

        $soql = sprintf("SELECT Id, Name, ClickpdxCatalog__HtmlDescription__c FROM Product2 WHERE Id IN (%s)", $step2);




        
        //$builder = DatabaseUtils::parse("SELECT Id, Name, Description FROM Product2 WHERE Id = '%s'", $productIds);

        //select * from product2 where id in ('123', '124')

        //var_dump($builder);exit;


        // How will we get this to work when needing to pass an array of Product IDs in?
        $result = $api->query($soql);


        $products = $result->getRecords();

        
        // var_dump($products);exit;
 

        $desc = array_map(function($product) {
            return empty($product['ClickpdxCatalog__HtmlDescription__c']) ? $product["Description"] : $product['ClickpdxCatalog__HtmlDescription__c'];
        }, $products);


    
        $qlsnippets = sprintf("CALL SNIPPETS(('%s'), 'ocdla_products', '%s', 10 AS around, 300 AS limit, 1 AS query_mode, 'strip' AS html_strip_mode, '<mark class=\"result\">' AS before_match, '</mark>' AS after_match)",implode("','",$desc),$terms);

        $snippets = mysqli_query($conn, $qlsnippets);
        

        
        $counter = 0;
        while($row = mysqli_fetch_assoc($snippets)) {
            // var_dump($row);
            $snippet = '<div style="line-height:15px;">'.$row['snippet'].'</div>';
            $product = $products[$counter];
            
            $shoplink = "{$domain}/OcdlaProduct?id={$product['Id']}";
            $name = "<h2 style='font-size:12pt;'><a href='{$shoplink}' target='_blank'>{$product['Name']}</a></h2>";
            
            //$description = array_values($rows)[$snippet];
            $html[] = '<div class="search-result" style="margin-bottom:14px;">'.$name.$snippet.'</div>';

            $counter++;
        }
        
        $title = "<h2>Showing results for <i>{$terms}</i></h2>";
        return $title . implode("\n", $html);
        //return "foobar";

        exit;
    }



    public function exampleSearchTest() {

        // If we're experimenting then let's not bother returning the theme.
        $debug = true;

        // Jose will update this IP with the public IP of OCDLA's database / search server.
        // This is the Elastic IP of the Database Server.
        $sphinxHost = "35.162.222.119"; 

        // This is our SphinxQL port - let's use SQL syntax to query the search engine.
        $sphinxQLPort = 9306; 

        // Alternate port when using the included SphinxApi.php library.
        $sphinxApiPort = 9312; 

        // We'll perform a secondary $api query for the actual Salesforce 
        // products using these IDs.
        $productIds = array();


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

        $conn = mysqli_connect($sphinxHost, "", "", "", $sphinxQLPort);
        if (!$conn) {
            echo "Error: Unable to connect to MySQL." . "<p>";
            echo "Debugging errno: " . mysqli_connect_errno() . "<p>";
            echo "Debugging error: " . mysqli_connect_error() . "<p>";
            exit;
        }
        
        $terms = "ocdla";
        $ql = "SELECT * FROM ocdla_products WHERE MATCH('%s')";

        $query = sprintf($ql,$terms);

        $result = mysqli_query($conn, $query);

        

        // Iterate through the query results.
        while($row = mysqli_fetch_assoc($result)) {
            $productIds[] = $row["product_id"];
            // var_dump($row);
        }

        // Our list of proudct IDs.
        var_dump($productIds);


        


        // Per usual.
        $api = $this->loadForceApi();


        
        $builder = DatabaseUtils::parse("SELECT Id, Name, Description FROM Product2 WHERE Id = '%s'", $productIds);

        var_dump($builder);exit;


        // How will we get this to work when needing to pass an array of Product IDs in?
        $result = $api->query();


        var_dump($result->getRecords());

        exit;  
        // This is where we can print off the description.
        foreach($results->getRecords() as $product) {


        }


        // Now we want to get SNIPPETS with the highlighted search term(s).

        


        if($debug) {
            exit;
        }
        else return "Using SphinxQL via Mysqli client library!";
    }
}