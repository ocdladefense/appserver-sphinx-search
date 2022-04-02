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

    protected static $registered = array(
        "wiki_main" => "SearchResultWiki",
        "ocdla_products" => "SearchResultProduct",
        "ocdla_members" => "SearchResultProduct"
    );

    private $isInitialized = null;
    




    public function getMatch($prop = "alt_id", $altId) {
        return array_filter(self::$matches, function($match) use($altId,$prop){ return $match[$prop] == $altId; });
    }

    public function addMatch($result)
    {
        $id = $result["id"];
        $index = $result["indexname"];

        self::$matches[$id]= $result;
    }

    public function register($map) {

    }


    public static function buildSnippets()
    {   
        $qlsnippets = SphinxQL::call("snippets", $docs, $this->index, $terms);
        $snippets = mysqli_query($conn, $qlsnippets);
    }   


    public function init() {

        // Delegate to the appropriate registered
        // result classes.
        $indexes = array_map(function($match) { return $match["indexname"]; }, self::$matches);
        $indexes = array_unique($indexes);
        
        array_walk($indexes, function($index) { 
            $class = self::$registered[$index];
            self::$handlers[$index] = new $class();
        });

        /*
        array_walk(self::$matches, function($match) {
            $index = $match["indexname"];
            $docId = $match["id"];
            $handler = self::$handlers[$index];
            $handler->addResult($docId,$match);
        });
        */

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

        $handler = self::$handlers["wiki_main"];
        $altIds = $handler->getDocumentIds();
        $handler->loadDocuments($altIds);

        $handler = self::$handlers["ocdla_products"];
        $altIds = $handler->getDocumentIds();
        $handler->loadDocuments($altIds);

        return (function () {
            
            while($match = next(self::$matches)) {
            
                $index = $match["indexname"];
                $handler = self::$handlers[$index] ?? $this;
                
                $result = $handler->newResult($match["alt_id"]);
                
                yield $result;
            }
        })();

        // return new \ArrayObject($results);
    }


    protected function newResult($docId) {
        $match = self::$matches[$docId];
        $index = $match["indexname"];
        $altId = $match["alt_id"];
        $domain = "https://ocdla.force.com";
        $domain = "https://ocdla.my.salesforce.com";
        $url = $domain . "/" . $altId;
        return new SearchResult($altId,$snippet,$url);
    }


}