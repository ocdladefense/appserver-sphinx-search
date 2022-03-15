<?php

class SearchResultBase{
    protected static $customOptions;

    const COMMA_SPACE_SEPERATOR = ", ";

    protected static $defaultOptions = array(
        "around" => 10,
        "limit" => 300,
        "query_mode" => 1,
        "html_strip_mode" => "'strip'",
        "before_match" => "'<mark class=\"result\">'",
        "after_match" => "'</mark>'"
    );

    public static function setOption($name, $value)
    {

    }

    public static function getSPHINXQLSyntax($fn = "CALL SNIPPETS")
    {
        $arr = array();
        foreach(self::$defaultOptions as $name=>$value)
        {
            $arr[] = sprintf("%s AS %s", $value, $name);
        }
        $options = implode(self::COMMA_SPACE_SEPERATOR, $arr);
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