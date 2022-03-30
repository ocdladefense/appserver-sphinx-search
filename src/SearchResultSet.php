<?php


class SearchResultSet implements \IteratorAggregate {


    private static $docIds = array();

    private static $documents = array();

    private static $snippets = array();

    private static $terms = array();

    protected static $ids = array();

    protected static $results = array();

    protected $index = null;

    protected $handlers = array();

    private $isInitialized = null;
    

    public function addResult($result)
    {
         $this->results []= $result;
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
        $types = array_map(function($result) { return $result["indexname"]; }, $this->results);
        $types = array_unique($types);

        $handlers = $this->handlers;

        array_walk($types, function($type) use($handlers) { 
            $class = self::$registered[$type];
            $this->handlers[$type] = new $class();
        }, $types);

        

        $this->isInitialized = true;
    }


    public function setResult($index, $docId, $result) {
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


        // then get the snippets

        $results = [
            new SearchResult("Title 1", "Snippet 1"),
            new SearchResult("Title 2", "Snippet 2")
        ];


        return new \ArrayObject($results);
    }


}