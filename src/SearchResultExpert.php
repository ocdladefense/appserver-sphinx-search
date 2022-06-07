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

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_experts" == $index;
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
        $previews = array_map(function($expert){
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
        }, $this->documents);

        $snippets = self::buildSnippets($previews, $this->index);

        $this->snippets = array_combine(array_keys($this->documents), $snippets);
    }



    public function newResult($docId) {
        $doc        = $this->documents[$docId];       
        $snippet    = $this->snippets[$docId];

        $title      = $doc["Name"];
        $snippet    = str_replace('&nbsp;', ' ', $snippet);

        $domain     = "https://ocdla.app";
        $result     = new SearchResult($title,$snippet,"{$domain}/directory/members/{$docId}");
        $result->setTemplate("expert");

        return $result;
    }
       
}