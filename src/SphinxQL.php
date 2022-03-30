<?php



class SphinxQL {

    

    protected $conn = null;

    protected $host = null;

    protected $port = null;

    protected $customOptions;

    const COMMA_SPACE_SEPARATOR = ", ";

    const COMMA_SEPARATOR = ",";

    const SPHINX_QL_PORT = 9306;


    protected static $defaultOptions = array(
        "around" => 10,
        "limit" => 300,
        "query_mode" => 1,
        "html_strip_mode" => "'strip'",
        "before_match" => "'<mark class=\"result\">'",
        "after_match" => "'</mark>'"
    );


    public function __construct($sphinxHost, $port = 9306) {
        $this->host = $sphinxHost;
        $this->port = $port ?? self::SPHINX_QL_PORT;
    }


    public function connect() {

        // Example CLI commands for testing availability of searchd.
        // mysql -P9306 -h35.162.222.119 -protocol=tcp --prompt='sphinxQL> '"
        // $mysqli = new Mysqli("35.162.222.119:9306","","","");


        $this->conn = mysqli_connect($this->host, "", "", "", $this->port);
        if (!$this->conn) {
            echo "Error: Unable to connect to MySQL." . "<p>";
            echo "Debugging errno: " . mysqli_connect_errno() . "<p>";
            echo "Debugging error: " . mysqli_connect_error() . "<p>";
            exit;
        }
    }


    public function query($query) {

        //@@relaxed
        return mysqli_query($this->conn, $query);
    }


    public static function setOption($name, $value)
    {

    }

    public static function getSPHINXQLSyntax($fn = "CALL SNIPPETS")
    {
        $arr = array();
        // Test for is_numeric or is_string then format the value accordingly,
        // for example, string get wrapped in single quotes (');
        foreach(self::$defaultOptions as $name=>$value)
        {
            $arr[] = sprintf("%s AS %s", $value, $name);
        }
        $options = implode(self::COMMA_SPACE_SEPARATOR, $arr);
        //return "CALL SNIPPETS((%s), '$indexName', '$terms', $options)";

        //Note: Parentheses are unnecessary for a single piece of data
        //return "CALL SNIPPETS(%s, '%s', '%s', $options)";
        
        return "CALL SNIPPETS((%s), '%s', '%s', $options)";
    }

    public static function getCallSnippets($documents, $indexName, $terms)
    {
        $arr = self::prepareData($documents);
        $data = self::stringify($arr);
        $syntax = self::getSPHINXQLSyntax();
        return sprintf($syntax, $data, $indexName, $terms);
    }

    public static function prepareData($documents)
    {
        return array_map(function($doc){
            return addslashes($doc);
        }, $documents);
    }

    public static function stringify($documents)
    {
        $step1 = implode("','", $documents);
        return  sprintf("'%s'", $step1);
    }

    public static function setOptions($around = 10, $limit = 300, $queryMode = 1, $htmlStripMode = 'strip', $beforeMatch = '<mark class=\"result\">', $afterMatch = '</mark>')
    {
        $options = array(); 
        if($around != null)
        {
            $around = "$around AS AROUND"; 
        }

    }



    
}