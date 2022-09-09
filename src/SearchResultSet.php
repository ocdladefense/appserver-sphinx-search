<?php


class SearchResultSet implements \IteratorAggregate {

    // @var handlers
    // Handlers are really additional ResultSet
    // instances.  We can delelagate document loading
    // and snippet loading to these subclasses.
    protected static $handlers = array();

    // Internal array of matches returned
    // by the search engine.  These matches might span
    // several different repositories.  The current version
    // returns an "indexname" key that specifies which index the result
    // was returned from.
    protected static $matches = array();

    // @var index
    // Specific index for which this class is a handler for.
    protected $index = null;

    // @var documents
    // Instance variable populated after
    // querying for the original documents.
    protected $documents = array();

    // @var snippets
    // 
    protected $snippets = array();

    // @var terms
    protected static $terms = array();

    // @var results
    // Store matches/results for a specific index.
    protected $results = array();

    // @var client
    // Stores the instance of SphinxQL
    protected static $client;

    protected static $registered = array(
        "wiki_main"             => "SearchResultWiki",
        "ocdla_products"        => "SearchResultProduct",
        "ocdla_members"         => "SearchResultMember",
        "ocdla_experts"         => "SearchResultExpert",
        "ocdla_car"             => "SearchResultCar",
        "ocdla_events"          => "SearchResultEvent",
        "ocdla_videos"          => "SearchResultVideo"
    );

    private $isInitialized = null;
    

    public static function setClient($client){
        self::$client = $client;
        //var_dump($this->client);
        //exit;
    }

    public static function setTerms($terms){
        self::$terms = $terms;
    }


    public function getMatch($prop = "alt_id", $altId) {
        return array_filter(self::$matches, function($match) use($altId,$prop){ return $match[$prop] == $altId; });
    }



    public function addMatch($result)
    {
        $id = $result["id"];
        self::$matches[$id]= $result;
    }




    public static function buildSnippets($documents, $index, $terms = null)
    {   
        $terms = $terms ?? self::$terms;

  
        $spql = SphinxQL::getCallSnippets($documents, $index, $terms);
        
   
        $result = self::$client->query($spql);
        
        
  
        while($row = mysqli_fetch_assoc($result))
        {
            $snippets[] = $row["snippet"]; 
        }

        return $snippets;
    }   



    public function init() {

        // Delegate to the appropriate registered
        // result classes.
        $indexes = array_map(function($match) { return $match["indexname"]; }, self::$matches);
        $indexes = array_unique($indexes);
        
        array_walk($indexes, function($index) { 
            $class = self::$registered[$index];
            if(null != $class && class_exists($class)) {
                self::$handlers[$index] = new $class();
            }
        });

        $this->isInitialized = true;
    }


    // Add a handler-specific result.
    // Keyed by Sphinx document id, value is the alt_id for accessing
    // the original document.
    public function addResult($docId, $match) {
        $this->results[$docId] = $match["alt_id"];
    }


    public function getResult($docId) {
        return $this->results[$docId];
    }

    public function getResults() {
        return $this->results;
    }



    // This statement is executed when
    // we call our foreach() loop.
    public function getIterator()
    {
        
        if(!$this->isInitialized) {
            $this->init();
        }


        foreach(self::$handlers as $handler) {
            $altIds = $handler->getDocumentIds();
            $handler->loadDocuments($altIds);
            $handler->getSnippets();
        }

        
        return (function () {
            $match = current(self::$matches);
            do {

                $index = $match["indexname"];
                $handler = self::$handlers[$index] ?? $this;
                
                $result = $handler->newResult($match["alt_id"]);

                yield $result;
            } while($match = next(self::$matches));
        })();
    }


    protected function newResult($docId) {
        $match = $this->results[$docId];
        $index = $match["indexname"];
        $altId = $match["alt_id"];
        $result = new SearchResult($altId,$index,$altId);
        
        $result->setTemplate($index);

        return $result;
    }


}