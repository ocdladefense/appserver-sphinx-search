<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultProduct extends SearchResultSet implements ISnippet {

    private static $query = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c FROM Product2 WHERE Id IN (%s)";


    private $template = "product";



    public function __construct()
    {
        $this->index = "ocdla_products";
    }



    public function getDocumentIds() {

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_products" == $index) $this->results[$altId] = $match;
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

        foreach($products as $product) {
            $this->results[$product["Id"]] = $product;
        }

        /*
        $desc = array_map(function($product) {
            $html = $product['ClickpdxCatalog__HtmlDescription__c'];
            $standard = $product["Description"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
        }, $products);
        */
    }






    public function newResult($docId) {
        $result = $this->results[$docId];       
        $title = $result["Name"];
        $snippet = $result["ClickpdxCatalog__HtmlDescription__c"];
        $domain = "https://ocdla.force.com";
        $result = new SearchResult($title,$snippet,"{$domain}/OcdlaProduct?id={$docId}");
        $result->setTemplate("product");

        return $result;
    }
       
}