<?php

class SearchResultBase{
    private $callSnippets = "CALL SNIPPETS(('%s'), '%s', '%s', 10 AS around, 300 AS limit, 1 AS query_mode, 'strip' AS html_strip_mode, '<mark class=\"result\">' AS before_match, '</mark>' AS after_match)";
    protected $data;

    protected $indexName;

    protected $terms;

    protected static $query;

    protected static $customOptions;

    protected static $defaultOptions = array(
        "around" => "10 AS AROUND",
        "limit" => "300 AS limit",
        "query_mode" => "1 AS query_mode",
        "html_strip_mode" => "'strip' AS html_strip_mode",
        "before_match" => "'<mark class=\"result\">' AS before_match",
        "after_match" => "'</mark>' AS after_match"
    );

    public static function getCallSnippets($documents, $indexName, $terms)
    {
        if($indexName == "ocdla_products")
        {
            $desc = array_map(function($product) {
                $html = $product['ClickpdxCatalog__HtmlDescription__c'];
                $standard = $product["Description"];
    
                $html = utf8_decode($html);
                $html = preg_replace('/\x{00A0}+/mis', " ", $html);
                
                return empty($html) ? $standard : $html;
            }, $documents);
            $options = implode(", ", self::$defaultOptions);
            //var_dump($options);
            //var_dump($desc);
        }
        elseif($indexName == "wiki_main")
        {
            $desc = array();
            foreach($documents as $result)
            {
                $desc[] = addslashes($result["old_text"]);
                //var_dump($result["page_id"]);
            }
            $options = implode(", ", self::$defaultOptions);
        }

        $data = implode("','" ,$desc);

        
        self::$query = "CALL SNIPPETS(('$data'), '$indexName', '$terms', $options)";
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