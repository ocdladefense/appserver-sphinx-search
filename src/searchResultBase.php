<?php

class SearchResultBase{
    private $callSnippets = "CALL SNIPPETS(('%s'), '%s', '%s', 10 AS around, 300 AS limit, 1 AS query_mode, 'strip' AS html_strip_mode, '<mark class=\"result\">' AS before_match, '</mark>' AS after_match)";
    protected $data;

    protected $indexName;

    protected $terms;

    protected $defaultOptions = array(
        "around" => "10 AS AROUND",
        "limit" => "300 AS limit",
        "query_mode" => "1 AS query_mode",
        "html_strip_mode" => "'strip' AS html_strip_mode",
        "before_match" => "'<mark class=\"result\">' AS before_match",
        "after_match" => "'</mark>' AS after_match"
    );

    public function getCallSnippets($data, $indexName, $terms)
    {
        //addslashes 
        $options = implode(",", $defaultOptions);
        return "CALL SNIPPETS(('$data'), '$indexName', '$terms', $options)";
    }

    public function setOptions($indexName)
    {
        $indexOptions = array(
            "ocdla_products" => $defaultOptions,
            "wiki_main" => $otherOptions
        );
    }
}