<?php

class SearchResultWikiMain{
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
        $quote = "A strong odor of marijuana emanating from a person is enough for reasonable suspicion to stop a person to investigate possession of a criminal amount of marijuana. Reasonable suspicion does not require the officer to articulate his belief that the stopped person possesses a criminal amount of marijuana. Here, defendant was a passenger during a traffic stop. The driver consented to a search of the car and the police officer directed the passengers to get out of the car. When defendant got out of the car the officer smelled a “strong odor of marijuana” around the defendant and believed the defendant was in possession of a large amount marijuana. The officer asked for consent to search defendant’s pockets and he consented. Thus, the officer reasonably and legally inferred that defendant was carrying a large amount of marijuana. Interestingly, it's not clear this case comes out differently post-legalization. The court's blunt logic that \"an officer who smells a strong odor of marijuana emanating from a person can reasonably infer that that person is carrying a large amount of marijuana\" would seem to apply to 4 ounces if it applies to 1 ounce. Of course, defense attorneys should argue that possession of a \"large amount of marijuana\" is now legal and one can't distinguish by smell a larger amount.  [http://www.publications.ojd.state.or.us/docs/A151670.pdf State v Vennell], 274 Or App 94 (2015).";

        $quote = addslashes($quote);

        $fn = function($id){return "'{$id}'";};
        $step1 = array_map($fn, self::$ids);
        $docIds = implode( ",", $step1);
        $query = "SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)";
        $results = $db->select($query);
        $queryTest = "SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ('4001')";
        $resultTest = $db->select($queryTest);
        var_dump($resultTest);

        /*//$snippetTest = sprintf("CALL SNIPPETS('%s', 'wiki_main', '%s', 10 AS around, 300 AS limit, '<mark class=\"result\">' AS before_match, 'strip' AS html_strip_mode, '</mark>' AS after_match)", $quote, $terms);
        $snippetTest = sprintf("CALL SNIPPETS('%s', 'wiki_main', '%s', 10 AS around, 300 AS limit, 1 AS query_mode, 'strip' AS html_strip_mode)", $quote, $terms);
        print($snippetTest);
        $snippets = mysqli_query($conn, $snippetTest);
        var_dump($snippets);*/

        foreach($resultTest as $test)
        {
            /*var_dump($test);
            print($test["old_text"]);
            $warning = "SHOW WARNINGS";

            $snippetTest = sprintf("CALL SNIPPETS('%s', 'wiki_main', '%s', 10 AS around, 300 AS limit, '<mark class=\"result\">' AS before_match, AS html_strip_mode, '</mark>' AS after_match)", , $terms);
            $snippets = mysqli_query($conn, $snippetTest);
            $showWarnings = mysqli_query($conn, $warning);
            var_dump($snippets);*/
        }

        $step1 = array();
        foreach($results as $result)
        {
            $step1[] = addslashes($result["old_text"]);
            //var_dump($result["page_id"]);
        }
        //var_dump($step1);
        $step2 = implode("','", $step1);
        
        $qlsnippets = sprintf("CALL SNIPPETS(('%s'), 'wiki_main', '%s', 10 AS around, 300 AS limit, 'strip' AS html_strip_mode)", implode("','", $step1), $terms);
        //var_dump($qlsnippets);
        $snippets = mysqli_query($conn, $qlsnippets);
        var_dump($snippets);
        exit;

    }
    
}