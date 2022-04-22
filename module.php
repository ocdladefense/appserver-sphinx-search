<?php




class SphinxModule extends Module {


    private static $registered = array(
        "ocdla_products"        => "SearchResultProduct",
        "wiki_main"             => "SearchResultWiki",
        "ocdla_members"         => "SearchResultMember",
        "ocdla_experts"         => "SearchResultExpert",
        "ocdla_car"             => "SearchResultCar"
    );


    // If we're experimenting then let's not bother returning the theme.
    private $debug = true;

    // Jose will update this IP with the public IP of OCDLA's database / search server.
    // This is the Elastic IP of the Database Server.
    private $sphinxHost = "35.162.222.119"; 

    // This is our SphinxQL port - let's use SQL syntax to query the search engine.
    private $sphinxQLPort = 9306; 

    // Alternate port when using the included SphinxApi.php library.
    private $sphinxApiPort = 9312; 

    


    public function __construct() {

        parent::__construct();
    }


    // Main callback; return a call to the SphinxQL method.
    public function doSearch($terms = null) {
        if($terms == null)
        {
            $req = $this->getRequest();
            $data = $req->getBody();

            $terms = $data->term;
            if($terms == null)
            {
                throw new exception("Search cannot be null");
            }
        }

        return $this->searchUsingSphinxQL($terms);
    }





    /**
     * @method searchUsingSphinxQL
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function searchUsingSphinxQL($terms) {


        // Iterable so we can loop through results.
        // Register any secondary handlers.  
        // These will handle the loading of documents,
        // and any optional snippet generation.
        $results = new SearchResultSet();
        

        
        // Instantial a new SphinxQL client
        // that will make queries to the indexing service.
        $client = new SphinxQL($this->sphinxHost, $this->sphinxQLPort);
        $client->connect();


        $results->setClient($client);
        $results->setTerms($terms);


        // Query the specified indexes
        // for the keywords.
        $indexes = "ocdla_products, ocdla_car, ocdla_members, wiki_main";
        $format = "SELECT * FROM %s WHERE MATCH('%s')";
        $query = sprintf($format, $indexes, $terms);
        $matches = $client->query($query);


        while($match = mysqli_fetch_assoc($matches)) {
            // var_dump($match);
            $index = $match["indexname"];
            $alt_id = $match["alt_id"];
            $id = $match["id"];

            $results->addMatch($match);
        }

        // exit;

        // Testing code to see if the delegate classes 
        // can actually load documents.
        // $set = new SearchResultWiki();
        // $set->loadDocuments(5442);
        // var_dump($set->getResults());
        // exit;

        
        
        $widget = new Template("widget");
		$widget->addPath(__DIR__ . "/templates");

        $page = new Template("results");
        $page->addPath(__DIR__ . "/templates");
        $list = $page->render(
            array(
                "widget"    => $widget->render(),
                "terms"     => $terms,
                "results"   => $results
            )
        );

        
        return $list;
    }





    // If necessary, use the included SphinxApi client library.
    // See ocdladefense/lib-sphinx-search for more info.
    public function exampleSearchSphinxApi($terms) {



        $cl = new SphinxClient(); // Will it work?

        
        return "Using SphinxApi client library!";
    }



// If necessary, use the included SphinxApi client library.
    // See ocdladefense/lib-sphinx-search for more info.
    public function exampleSearchSphinxRestApi($terms) {



        $cl = new SphinxClient(); // Will it work?

        
        return "Using SphinxApi client library!";
    }

    

}