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
        $this->index = "ocdla_cars";
    }



    public function getDocumentIds() {

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_cars" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
    }




    public function loadDocuments($productIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $productIds);

        $result = $api->query($soql);

        $products = $result->getRecords();

        foreach($cars as $car) {
            $this->results[$product["Id"]] = $product;
        }
        // var_dump($this->results);exit;

    }






    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["title"];
        $snippet    = $result["summary"];

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/car/{$docId}");
        $result->setTemplate("car");

        return $result;
    }
       
}