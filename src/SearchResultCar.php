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

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_car" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
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
            //var_dump($car["id"]);
        }
        //exit;
        //var_dump($this->results);exit;

    }


    public function getSnippets()
    {
        $summary = array_map(function($car){
            return $car["summary"];
        }, $this->results);

        $this->documents = $summary;

        $this->buildSnippets();
    }



    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["title"];
        //$snippet    = $result["summary"];

        $snippet    = array_shift($this->snippets);

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/car/list/{$docId}");
        $result->setTemplate("car");

        return $result;
    }
       
}