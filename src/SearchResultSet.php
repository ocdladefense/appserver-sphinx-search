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
        "ocdla_members" => "SearchResultMember"
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
        }

        
        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            $class = self::$registered[$index];
            if(null == $class || !class_exists($class)) {
                
                $this->results[$altId] = $match;
            }
        }
        
        
        return (function () {
            
            while($match = next(self::$matches)) {
            
                $index = $match["indexname"];
                $handler = self::$handlers[$index] ?? $this;
                
                $result = $handler->newResult($match["alt_id"]);
                
                yield $result;
            }
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