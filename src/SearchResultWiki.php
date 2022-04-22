<?php


use Mysql\Database;


class SearchResultWiki extends SearchResultSet implements ISnippet {
    
    // Which template file should be used to render results
    // for this index.
    private $template = "wiki";


    // Query where to retrieve the original
    // Document source; used for displaying snippets.
    private static $query = "SELECT page_id, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN (:array)";


    public function getSnippets(){}
    
    public function __construct()
    {
      $this->index = "wiki_main";
    }



    public function getDocumentIds() {

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("wiki_main" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
    }
    

    public function loadDocuments($pageIds) {
        $pageIds = is_array($pageIds) ? $pageIds : array($pageIds);

        $params = array(
            "host" => "35.162.222.119",
            "user" => "intern",
            "password" => "wEtktXd7",
            "name" => "lodwikitest"
        );


        Database::setDefault($params);
        $db = new Mysql\Database();
        
        $records = $db->select(self::$query,$pageIds);
        
       
        foreach($records as $record) {
            $altId = $record["page_id"];
            $this->results[$altId] = $record;
        }
    }



    public function newResult($docId) {
        $result = $this->results[$docId];       
        $title = $result["page_title"];
        $snippet = substr($result["old_text"],0,255);

        $result = new SearchResult($title,$snippet,"https://lod.ocdla.org/index.php?curid={$docId}");
        $result->setTemplate("wiki");

        return $result;
    }
        
        

    
}