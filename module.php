<?php




class SphinxModule extends Module {
  
  const COMMA_SEPARATED = ",";
  
  const REPOSITORIES = array(
    "people" => array(
      "display" => "People", 
      "id" => "People",
      "name" => "ocdla_members",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search OCDLA members, expert witnesses, and judges."
    ),
    "places" => array(
      "display" => "Places", 
      "id" => "Places",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search cities and counties."
    ),
    "videos" => array(
      "display" => "Videos", 
      "id" => "Videos",
      "name" => "videos",
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search video transcripts from OCDLA seminars and events."
    ),
    "library" => array(
      "display" => "Library of Defense", 
      "id" => "Library",
      "name" => "wiki_main",
      "active" => true,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search Library of Defense subject articles."
    ),
    "blog" => array(
      "display" => "Blog", 
      "id" => "Blog",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search Library of Defense blog posts."
    ),
    "car" => array(
      "display" => "Case Reviews", 
      "id" => "Car",
      "name" => "ocdla_car",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search Criminal Appellate Review summaries."
    ),
    "publications" => array(
      "display" => "Publications", 
      "id" => "Publications",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search OCDLA publications."
    ),
    "products" => array(
      "display" => "Products", 
      "id" => "Products",
      "name" => "ocdla_products",
      "active" => true,
      "Render" => true,
      "Checked" => true,
      "Description" => "Search OCDLA products."
    ),
    "events" => array(
      "display" => "Seminars & Events", 
      "id" => "Events",
      "name" => "ocdla_events",
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search OCDLA Events."
    ),
    "motions" => array(
      "display" => "Motions", 
      "id" => "Motions",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search the legacy motion bank."
    ),
    "ocdla.org" => array(
      "display" => "ocdla.org", 
      "id" => "ocdla",
      "name" => "wiki_main",
      "active" => false,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search the ocdla.org website."
    )
  );



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
    private $sphinxHost = "54.189.138.226";

    // This is our SphinxQL port - let's use SQL syntax to query the search engine.
    private $sphinxQLPort = 9306; 

    // Alternate port when using the included SphinxApi.php library.
    private $sphinxApiPort = 9312; 

    


    public function __construct() {

        parent::__construct();
    }


    public function displaySearchForm() {

      $search = new Template("search");
      $search->addPath(__DIR__ . "/templates");
      
      return !empty($_GET["q"]) ? $this->doSearch($_GET["q"]) : $search;
    }


    // Main callback; return a call to the SphinxQL method.
    public function doSearch($terms = null) {
        
        $req = $this->getRequest();
        $data = $req->getBody();
        //var_dump($data->repos);
        //var_dump($data->terms);
        //exit;
        // $terms = $data->terms
        $repos = $data->repos;// ?? SphinxModule::REPOSITORIES; // ocdla_car,ocdla_events

        $valid = null != $repos ? self::formatRepositories($repos) : self::getActiveRepositories(SphinxModule::REPOSITORIES);


        return $this->searchUsingSphinxQL($terms, $valid);
    }



    private static function formatRepositories($repos) {

      $selected = array_filter($repos, function($repo) {
        return array_key_exists($repo, SphinxModule::REPOSITORIES);
      });

      return self::getActiveRepositories($selected);
    }


    private static function getActiveRepositories($repos) {

      return array_filter($repos, function($repo) { return $repo["active"] === true; });
    }




    /**
     * @method searchUsingSphinxQL
     * 
     * Example method to connect to a remote SphinxSearch server,
     * and return search results from a MATCH query.
     */
    public function searchUsingSphinxQL($terms, $repos) {


        // Iterable so we can loop through results.
        // Register any secondary handlers.  
        // These will handle the loading of documents,
        // and any optional snippet generation.
        $results = new SearchResultSet();
        

        
        // Instantial a new SphinxQL client
        // that will make queries to the indexing service.
        $client = new SphinxQL($this->sphinxHost, $this->sphinxQLPort);
        $client->connect();

        //var_dump($terms);
        //exit;

        $results->setClient($client);
        $results->setTerms($terms);


        // Query the specified indexes
        // for the keywords.
        $nrepos = array_map(function($repo) { return $repo["name"]; }, $repos);
        $indexes = implode(self::COMMA_SEPARATED, $nrepos);
        $indexes = "wiki_main";
 
        //$indexes = "ocdla_products, ocdla_car, ocdla_members, wiki_main"; //CHECKHERE
        //var_dump($indexes);
        //exit;
        $format = "SELECT * FROM %s WHERE MATCH('%s')";
        $query = sprintf($format, $indexes, $terms);
        $matches = $client->query($query);

        //var_dump($matches);
        //exit;

        while($match = mysqli_fetch_assoc($matches)) {
            // var_dump($match);
            $index = $match["indexname"];
            $alt_id = $match["alt_id"];
            $id = $match["id"];

            $results->addMatch($match);
            //var_dump($match);
        }
        //var_dump($results);
        //exit;

        // Testing code to see if the delegate classes 
        // can actually load documents.
        // $set = new SearchResultWiki();
        // $set->loadDocuments(5442);
        // var_dump($set->getResults());
        // exit;

        
        // var_dump($repos);exit;


        $widget = new Template("widget-checkboxes");
		    $widget->addPath(__DIR__ . "/templates");
        $widgetHTML = $widget->render(array("repos" => $repos, "q" => $terms));

        $page = new Template("results");
        $page->addPath(__DIR__ . "/templates");
        $list = $page->render(
            array(
                "widget"    => $widgetHTML,
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