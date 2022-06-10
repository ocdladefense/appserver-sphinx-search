<?php




class SphinxModule extends Module {
  
  const COMMA_SEPARATED = ",";
  
  const REPOSITORIES = array(
    "ocdla_members" => array(
      "key" => "people",
      "display" => "People", 
      "id" => "People",
      "name" => "ocdla_members",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search OCDLA members, expert witnesses, and judges."
    ),
    "places" => array(
      "key" => "places",
      "display" => "Places", 
      "id" => "Places",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search cities and counties."
    ),
    "ocdla_videos" => array(
      "key" => "videos",
      "display" => "Videos", 
      "id" => "Videos",
      "name" => "ocdla_videos",
      "active" => true,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search video transcripts from OCDLA seminars and events."
    ),
    "wiki_main" => array(
      "key" => "library",
      "display" => "Library of Defense", 
      "id" => "Library",
      "name" => "wiki_main",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search Library of Defense subject articles."
    ),
    "ocdla.org" => array(
      "key" => "ocdla.org",
      "display" => "ocdla.org", 
      "id" => "ocdla",
      "name" => "wiki_main",
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search the ocdla.org website."
    ),
    "blog" => array(
      "key" => "blog",
      "display" => "Blog", 
      "id" => "Blog",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search Library of Defense blog posts."
    ),
    "ocdla_car" => array(
      "key" => "car",
      "display" => "Case Reviews", 
      "id" => "Car",
      "name" => "ocdla_car",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search Criminal Appellate Review summaries."
    ),
    "publications" => array(
      "key" => "publications",
      "display" => "Publications", 
      "id" => "Publications",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search OCDLA publications."
    ),
    "ocdla_products" => array(
      "key" => "products",
      "display" => "Products", 
      "id" => "Products",
      "name" => "ocdla_products",
      "active" => true,
      "Render" => true,
      "Checked" => true,
      "Description" => "Search OCDLA products."
    ),
    "ocdla_events" => array(
      "key" => "events",
      "display" => "Seminars & Events", 
      "id" => "Events",
      "name" => "ocdla_events",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search OCDLA Events."
    ),
    "motions" => array(
      "key" => "motions",
      "display" => "Motions", 
      "id" => "Motions",
      "name" => null,
      "active" => false,
      "Render" => false,
      "Checked" => false,
      "Description" => "Search the legacy motion bank."
    ),
    "ocdla_experts" => array(
      "key" => "experts",
      "display" => "Experts",
      "id" => "witness", 
      "name" => "ocdla_experts",
      "active" => true,
      "Render" => true,
      "Checked" => false,
      "Description" => "Search through expert witness."
    )
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
        $repos = $_GET["repos"];//$data->repos;// ?? SphinxModule::REPOSITORIES; // ocdla_car,ocdla_events

        $valid = null != $repos ? self::formatRepositories($repos) : self::getActiveRepositories(SphinxModule::REPOSITORIES);


        return $this->searchUsingSphinxQL($terms, $valid);
    }



    private static function formatRepositories($repos) {
      
      $selected = array_filter($repos, function($repo) {
        return array_key_exists($repo, SphinxModule::REPOSITORIES);
      });

      $arr = array();
      foreach ($selected as &$value) {
        $arr[] = SphinxModule::REPOSITORIES[$value];
      }
      
      return self::getActiveRepositories($arr);
    }


    private static function getActiveRepositories($repos = null) {
      $repos = $repos ?? SphinxModule::REPOSITORIES;
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

        //var_dump($repos);
        //exit;

        $results->setClient($client);
        $results->setTerms($terms);


        // Query the specified indexes
        // for the keywords.

        $nrepos = array_map(function($repo) { return $repo["name"]; }, $repos);
        $indexes = implode(self::COMMA_SEPARATED, $nrepos);
        //$indexes = "ocdla_experts";
 

        //$indexes = "ocdla_products, ocdla_car, ocdla_members, wiki_main"; //CHECKHERE
        //var_dump($indexes);
        //exit;
        $format = "SELECT * FROM %s WHERE MATCH('%s')";
        $query = sprintf($format, $indexes, $terms);
        $matches = $client->query($query);

        // var_dump($matches);
        //exit;

        while($match = mysqli_fetch_assoc($matches)) {
            $index = $match["indexname"];
            $alt_id = $match["alt_id"];
            $id = $match["id"];

            $results->addMatch($match);
            // var_dump($match);
        }

        // Testing code to see if the delegate classes 
        // can actually load documents.
        // $set = new SearchResultWiki();
        // $set->loadDocuments(5442);
        // var_dump($set->getResults());
        // exit;

        
        // var_dump($repos);exit;


        $widget = new Template("widget-checkboxes");
		    $widget->addPath(__DIR__ . "/templates");

        $widgetHTML = $widget->render(array(
          "repos"     => self::getActiveRepositories(),
          "selected"  => $repos,
          "q"         => $terms
        ));


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