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

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_members" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
    }




    public function loadDocuments($contactIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $contactIds);

        $result = $api->query($soql);

        $contacts = $result->getRecords();

        foreach($contacts as $contact) {
            $this->results[$contact["Id"]] = $contact;
        }
        // var_dump($this->results);exit;
    }






    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["Name"];
        $status    = $result["Ocdla_Member_Status__c"];
        

        $map = array(
            "A" => "Law Student",
            "S" => "Sustaining Member",
            "L" => "Lifetime Member",
            "R" => "Member",
            "H" => "Honored Member"
        );
        $snippet = "OCDLA " . ($map[$status] ?? "Member");

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/directory/members/{$docId}");
        $result->setTemplate("member");

        return $result;
    }
       
}