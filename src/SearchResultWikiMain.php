<?php

class SearchResultWikiMain extends SearchResultBase{
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
        //SELECT page_id, page_title, page_namespace FROM page WHERE page_id = $docId
        /*
            Refer to Database::setDefault()
            $api = $this->loadForceApi()
            SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)
            https://libraryofdefense.ocdla.org/page_namespace

        */

        $db = new Mysql\Database();
        $fn = function($id){return "'{$id}'";};
        $step1 = array_map($fn, self::$ids);
        $docIds = implode( ",", $step1);
        $query = "SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)";
        //$results = $db->select($query);
        //$queryTest = "SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ('4001')";
        $resultTest = $db->select($query);
        $blogs = $resultTest->getIterator();
        //var_dump($resultTest);
        //exit;
        
        //$qlsnippets = sprintf("CALL SNIPPETS(('%s'), 'wiki_main', '%s', 10 AS around, 300 AS limit, 'strip' AS html_strip_mode)", implode("','", $step1), $terms);
        //var_dump($qlsnippets);
        self::getCallSnippets($blogs, "wiki_main", $terms);
        $qlsnippets = self::$query;
        $snippets = mysqli_query($conn, $qlsnippets);
        //var_dump($snippets);
        //exit;

        $counter = 0;
        while($row = mysqli_fetch_assoc($snippets)){
            $snippet = $row["snippet"];

            $snippet = str_replace('&nbsp;', ' ', $snippet);
            $snippet = '<div style="line-height:15px;">'.$snippet."</div>";
            $blog = $blogs[$counter];

            $link = "https://libraryofdefense.ocdla.org/{$blog['page_title']}";
            $name = "<h2 style='font-size:12pt;'><a href='{$link}' target='_blank'>{$blog['page_title']}</a></h2>";

            self::enqueue('<div class="search-result" style="margin-bottom:14px;">'.$name.$snippet.'</div>');

            $counter++;
        }

        //var_dump(self::$results);
        //exit;
    }
    
}