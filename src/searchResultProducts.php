<?php

class SearchResultProducts extends SearchResultBase{

    private static $ids = array();

    private static $results = array();

    public function __construct()
    {

    }

    public static function addResult($r)
    {
        self::$ids[] = $r;
    }

    public static function enqueue($r)
    {
        self::$results[] = $r;
    }

    public static function dequeue()
    {
        $result = array_shift(self::$results);
        return $result;
    }

    public static function buildSnippets($terms, $conn, $api)
    {

        //var_dump(self::$ids);

        $fn = function($id){return "'{$id}'";};

        $step1 = array_map($fn, self::$ids);


        $step2 = implode(",", $step1);
      


        //var_dump($step);


        // Per usual.
        

        $soql = sprintf("SELECT Id, Name, Description, ClickpdxCatalog__HtmlDescription__c FROM Product2 WHERE Id IN (%s)", $step2);

        $result = $api->query($soql);


        $products = $result->getRecords();

        /*$desc = array_map(function($product) {
            $html = $product['ClickpdxCatalog__HtmlDescription__c'];
            $standard = $product["Description"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
        }, $products);

        var_dump($desc);*/
    
        self::getCallSnippets($products, "ocdla_products", $terms);
        $sqlsnippets = self::$query;
        //print($sqlsnippets);
        //exit;
        

        $snippets = mysqli_query($conn, $sqlsnippets);
        //var_dump($snippets);
        //exit;


        
        $counter = 0;
        while($row = mysqli_fetch_assoc($snippets)) {
        
            $snippet = $row["snippet"];

            //Each type of search result will have its own class.
            
            $snippet = str_replace('&nbsp;', ' ', $snippet);
            $snippet = '<div style="line-height:15px;">'.$snippet."</div>";
            $product = $products[$counter];
            
            $shoplink = "{$domain}/OcdlaProduct?id={$product['Id']}";
            $name = "<h2 style='font-size:12pt;'><a href='{$shoplink}' target='_blank'>{$product['Name']}</a></h2>";
            
            //$html[] = '<div class="search-result" style="margin-bottom:14px;">'.$name.$snippet.'</div>';
            self::enqueue('<div class="search-result" style="margin-bottom:14px;">'.$name.$snippet.'</div>');

            $counter++;
        }
        //var_dump(self::$results);
        //$value = self::dequeue();
        //var_dump($value);
        //exit;
    }
}