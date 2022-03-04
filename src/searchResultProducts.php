<?php

class SearchResultProducts{
    private $conn;

    private static $ids = array();

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public static function addResult($r)
    {
        self::$ids[] = $r;
    }

    public function buildSnippets($prodIds)
    {
        $productIds = array();

        $prodString = implode(",", $prodIds);
        //var_dump($prodString);
        //exit;
        $qlprod = "SELECT * FROM ocdla_products WHERE Id IN (%s)";
        $query = sprintf($qlprod, $prodString);

        $result = mysqli_query($conn, $query);
        //var_dump($result);

        while($row = mysqli_fetch_assoc($result))
        {
            $productIds[] = $row["product_id"];
            //var_dump($row);
        }

        if(count($productIds) < 1) {
            return "No results found.";
        }

        $fn = function($id){return "'{$id}'";};

        $step1 = array_map($fn, $productIds);


        $step2 = implode(",", $step1);


        // Per usual.
        

        $soql = sprintf("SELECT Id, Name, ClickpdxCatalog__HtmlDescription__c FROM Product2 WHERE Id IN (%s)", $step2);


    }
}