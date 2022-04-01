<?php


class SearchResultSet implements \IteratorAggregate {

    // Internal array of matches returned
    // by the search engine.
    private $matches = array();

    private static $docIds = array();

    private static $documents = array();

    private static $snippets = array();

    private static $terms = array();

    protected static $ids = array();

    protected static $results = array();

    protected $index = null;

    protected $handlers = array();

    private $isInitialized = null;
    

    public function addMatch($result)
    {
        $id = $result["id"];
        $index = $result["indexname"];

        $this->matches[$id]= $result;
    }

    public function register($map) {

    }


    public static function buildSnippets()
    {   
        $index = "wiki_main";
        $qlsnippets = SphinxQL::call("snippets", $docs, "wiki_main", $terms);
        $snippets = mysqli_query($conn, $qlsnippets);
    }   


    public function init() {

        // Delegate to the appropriate registered
        // result classes.
        $types = array_map(function($result) { return $result["indexname"]; }, $this->matches);
        $types = array_unique($types);

        array_walk($types, function($type) { 
            $class = self::$registered[$type];
            $this->handlers[$type] = new $class();
        }, $types);

        $this->isInitialized = true;
    }


    public function addResult($index, $docId, $result) {

        $handler = $this->handlers[$index];

        $handler->addResult($docId, $result);
    }


    public function getResult($docId) {
        $result = $this->results[$docId];

        $handler = $this->handlers[$result["indexname"]];

    }

    // This statement is executed when
    // we call our foreach() loop.
    public function getIterator()
    {
        /*
        if(!$this->isInitialized) {
            $this->init();
        }

        foreach($this->results as $result) {
            $docId = $result["id"];
            $handler = $result["indexname"];
            
            $this->addResult($handler, $docId, $result);
        }
        */

        // var_dump($types);

        // then load the documents for each class;

        $domain = "https://ocdla.force.com";
        // then get the snippets

        foreach($this->matches as $match) {
            // Load the documents
            $indexname = $match["indexname"];
        }

        return (function () {
            $domain = "https://ocdla.my.salesforce.com";
            while($match = next($this->matches)) {
                $result = new SearchResult($match["alt_id"],$match["indexname"]);
                $result->setUrl($domain . "/" . $match["alt_id"]);
                yield $result;
            }
        })();

        return new \ArrayObject($results);
    }


}