<?php


use Mysql\Database;


class SearchResultWiki extends SearchResultSet {
    


    public function __construct()
    {
      $this->index = "wiki_main";
    }

    public function loadDocuments() {

        $params = array(
            "host" => "172.31.47.173",
            "user" => "intern",
            "password" => "wEtktXd7",
            "name" => "lodwikitest"
        );


        Database::setDefault($params);




        $db = new Mysql\Database();

        $fn = function($id){return "'{$id}'";};
        $step1 = array_map($fn, self::$ids);
        $docIds = implode( ",", $step1);
        $query = "SELECT page_id, 'wiki_main' AS indexname, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)";

        //Returns DbSelectResult
        $resultTest = $db->select($query);
        $blogs = $resultTest->getIterator();
        $data = $resultTest->getValues("old_text");
    }



    public function getSearchResult() {
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
    }
    
}