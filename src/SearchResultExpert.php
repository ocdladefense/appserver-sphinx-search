<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultExpert extends SearchResultSet implements ISnippet {

    private static $query = "SELECT Id, Name, (SELECT Interest__c FROM AreasOfInterest__r) FROM Contact WHERE Id IN (%s)";


    private $template = "expert";



    public function __construct()
    {
        $this->index = "ocdla_experts";
    }

    public function getDocumentIds() {

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_experts" == $index) $this->results[$altId] = $match;
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


    public function getSnippets()
    {
        $desc = array_map(function($expert){
            if($expert["AreasOfInterest__r"] != null)
            {
                $expertise = $expert["AreasOfInterest__r"];
                $records = $expertise["records"];
                foreach($records as $record)
                {
                    $description .= $record["Interest__c"] . " ";
                }
            
            }
            else
            {
                $description = " ";
            }
            return $description;
        }, $this->results);

        $this->documents = $desc;

        $this->buildSnippets();        
    }



    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["Name"];
        $snippet    = array_shift($this->snippets);
        $snippet    = str_replace('&nbsp;', ' ', $snippet);

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/directory/members/{$docId}");
        $result->setTemplate("expert");

        return $result;
    }
       
}