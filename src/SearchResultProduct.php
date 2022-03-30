<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */



class SearchResultProduct extends SearchResultSet {

    const docQuery = "SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c FROM Product2 WHERE Id IN (%s)";


    



    public function __construct()
    {
        $this->index = "ocdla_products";
    }


    protected function loadDocuments()
    {


        $fn = function($id){return "'{$id}'";};

        $step1 = array_map($fn, self::$ids);


        $step2 = implode(",", $step1);


        // Per usual.
        

        $soql = sprintf(self::docQuery, $step2);

        $result = $api->query($soql);


        $products = $result->getRecords();

        $desc = array_map(function($product) {
            $html = $product['ClickpdxCatalog__HtmlDescription__c'];
            $standard = $product["Description"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
        }, $products);
    }






    public function ()
    {
        while($row = mysqli_fetch_assoc($snippets)) {
        
            $product = $products[$counter];

            $snippet = $row["snippet"];
            $url = "{$domain}/OcdlaProduct?id={$row['alt_id']}";
            //Each type of search result will have its own class.
            


            $counter++;
        }
    }
}