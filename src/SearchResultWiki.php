<?php


use Mysql\Database;


class SearchResultWiki extends SearchResultSet {
    

    private $template = "wiki";


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

        $page_ids = array();

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            if("wiki_main" == $index) $page_ids []= $match["alt_id"];
        }

        $db = new Mysql\Database();



        $fn = function($id){return "'{$id}'";};
        $step1 = array_map($fn, $page_ids);
        $docIds = implode( ",", $step1);
        $query = "SELECT page_id, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN ($docIds)";

        //Returns DbSelectResult
        $records = $db->select($query);
       
       
        foreach($records as $record) {
            $alt_id = $record["page_id"];
            $match = $this->getMatch("alt_id", $alt_id)[0];
            $id = $match["id"];
            $this->documents[$id] = $record;
        }
    }



    public function newResult($docId) {
        $altId = $this->results[$docId];       
        $title = "Wiki Title - $altId"; //$this->documents[$docId]["page_title"];
        $snippet = "Wiki Snippet - $altId"; //substr($this->documents[$docId]["old_text"],0,255);
        

        $result = new SearchResult($title,$snippet,"https://lod.ocdla.org/index.php?curid={$altId}");
        $result->setTemplate("wiki");

        return $result;
    }
    
}