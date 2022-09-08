<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultMember extends SearchResultSet implements ISnippet {

    private static $query = "SELECT Id, Name, Ocdla_Member_Status__c FROM Contact WHERE Id IN (%s)";


    private $template = "member";



    public function __construct()
    {
        $this->index = "ocdla_members";
    }

    

    public function getDocumentIds() {

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_members" == $index;
        };

        $filtered = array_filter(self::$matches, $filter);

        return array_map(function($match) { return $match["alt_id"]; }, $filtered);
    }




    public function loadDocuments($recordIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $recordIds);

        $result = $api->query($soql);

        $docs = $result->getRecords();

        $keys = array_map(function($doc) { return $doc["Id"]; }, $docs);
       
        $this->documents = array_combine($keys,$docs);
    }


    public function getSnippets()
    {
        
    }



    public function newResult($docId) {
        $doc        = $this->documents[$docId];       
        // $snippet    = $this->snippets[$docId]; 

        $title      = $doc["Name"];
        $status    = $doc["Ocdla_Member_Status__c"];
        

        $map = array(
            "A" => "Law Student",
            "S" => "Sustaining Member",
            "L" => "Lifetime Member",
            "R" => "Member",
            "H" => "Honored Member"
        );
        $snippet = "OCDLA " . ($map[$status] ?? "Member");

        $domain     = APP_URL;
        $result     = new SearchResult($title,$snippet,"{$domain}/directory/members/{$docId}");
        $result->setTemplate("member");

        return $result;
    }
       
}