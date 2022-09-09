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

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_products" == $index;
        };

        $filtered = array_filter(self::$matches, $filter);

        return array_map(function($match) { return $match["alt_id"]; }, $filtered);
    }


    

    public function loadDocuments($recordIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $recordIds);

        $result = $api->query($soql);

        $docs = $result->getRecords();

        $keys = array_map(function($doc) { return $doc["Id"]; }, $docs);
       
        $this->documents = array_combine($keys,$docs);
    }


    public function getSnippets()
    {
        $previews = array_map(function($product) {
            $html = $product['ClickpdxCatalog__HtmlDescription__c'];
            $standard = $product["Description"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
        }, $this->documents);

        $snippets = self::buildSnippets($previews, $this->index);

        $this->snippets = array_combine(array_keys($this->documents), $snippets);
    }



    public function newResult($docId) {
        $doc        = $this->documents[$docId];       
        $snippet    = $this->snippets[$docId];

        $title      = $doc["Name"];
        $snippet    = str_replace('&nbsp;', ' ', $snippet);

        $domain     = STORE_URL;
        $result     = new SearchResult($title,$snippet,"{$domain}/OcdlaProduct?id={$docId}");
        $result->setTemplate("product");

        return $result;
    }
       
}