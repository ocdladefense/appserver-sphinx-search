<?php


use Mysql\Database;


class SearchResultWiki extends SearchResultSet implements ISnippet {
    
    // Which template file should be used to render results
    // for this index.
    private $template = "wiki";


    // Query where to retrieve the original
    // Document source; used for displaying snippets.
    private static $query = "SELECT page_id, page_title, page_namespace, page_is_redirect, old_id, old_text FROM page, revision, text WHERE rev_id=page_latest AND old_id=rev_text_id AND page_id IN (:array)";


    
    
    public function __construct()
    {
      $this->index = "wiki_main";
    }



    public function getDocumentIds() {

        $filter = function($match) {
            $index = $match["indexname"];
            return "wiki_main" == $index;
        };

        $filtered = array_filter(self::$matches, $filter);

        return array_map(function($match) { return $match["alt_id"]; }, $filtered);
    }
    

    public function loadDocuments($pageIds) {
        $pageIds = is_array($pageIds) ? $pageIds : array($pageIds);

        $params = array(
            "host"      => SPHINX_DOC_HOST,
            "user"      => SPHINX_DOC_USER,
            "password"  => SPHINX_DOC_PASS,
            "name"      => SPHINX_DOC_WIKI
        );


        Database::setDefault($params);
        $db = new Mysql\Database();
        
        $records = $db->select(self::$query,$pageIds);
        

        foreach($records as $record) {
            $altId = $record["page_id"];
            $this->documents[$altId] = $record;
        }
    }

    public function getSnippets()
    {
        $previews = array_map(function($wiki){
            return $wiki["old_text"];
        }, $this->documents);

        if(null == $previews || count($previews) < 1) return array();
        
        $snippets = self::buildSnippets($previews, $this->index);

        $this->snippets = array_combine(array_keys($this->documents), $snippets);
    }

    public function newResult($docId) {
        $doc        = $this->documents[$docId];       
        $snippet    = $this->snippets[$docId];

        $title = $doc["page_title"];

        $result = new SearchResult($title,$snippet, LOD_URL . "/index.php?curid={$docId}");
        $result->setTemplate("wiki");

        return $result;
    }
        
        

    
}