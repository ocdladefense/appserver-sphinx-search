<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultCar extends SearchResultSet implements ISnippet {

    private static $query = "SELECT id, title, summary FROM car WHERE id IN(%s)";


    private $template = "car";



    public function __construct()
    {
        $this->index = "ocdla_car";
    }



    public function getDocumentIds() {

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_car" == $index;
        };

        $filtered = array_filter(self::$matches, $filter);

        return array_map(function($match) { return $match["alt_id"]; }, $filtered);
    }




    public function loadDocuments($carIds)
    {
        $params = array(
            "host" => "35.162.222.119",
            "user" => "intern",
            "password" => "wEtktXd7",
            "name" => "apptest"
        );

        Mysql\Database::setDefault($params);
        $db = new Mysql\Database();

        $cars = $db->select(self::$query,$carIds);
        

        foreach($cars as $car) {
            $this->results[$car["id"]] = $car;
        }
    }


    public function getSnippets()
    {
        $previews = array_map(function($car){
            return $car["summary"];
        }, $this->documents);

        $snippets = self::buildSnippets($previews, $this->index);

        $this->snippets = array_combine(array_keys($this->documents), $snippets);
    }



    public function newResult($docId) {
        $doc        = $this->documents[$docId];       
        $snippet    = $this->snippets[$docId];

        $title      = $doc["title"];

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/car/list/{$docId}");
        $result->setTemplate("car");

        return $result;
    }
       
}