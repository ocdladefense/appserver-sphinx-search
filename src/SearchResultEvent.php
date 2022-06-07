<?php
/**
 * @class SearchResultProduct
 * 
 * Represents a result set of Products and includes methods
 * to retrieve the original Product names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultEvent extends SearchResultSet implements ISnippet {

    private static $query = "SELECT Id, Name, Agenda__c, Overview__c, Banner_Location_Text__c, Venue__c FROM Event__c WHERE Id IN (%s)";


    private $template = "event";



    public function __construct()
    {
        $this->index = "ocdla_events";
    }



    public function getDocumentIds() {

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_events" == $index;
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
        $previews = array_map(function($event) {
            $agenda = $event["Agenda__c"] ?? " ";
            $overview = $event["Overview__c"] ?? " ";
            $banner = $event["Banner_Location_Text__c"] ?? " ";
            $venue = $event["Venue__c"] ?? " ";
            $description = $agenda . $overview . $banner . $venue;

            $description = strip_tags($description, "<br>");

            $description = str_replace("<br>", " ",$description);

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

        $domain     = "https://ocdla.force.com";
        $result     = new SearchResult($title,$snippet,"{$domain}/OcdlaProduct?id={$docId}");
        $result->setTemplate("event");

        return $result;
    }
       
}