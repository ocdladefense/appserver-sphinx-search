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

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_events" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
    }

    public function loadDocuments($eventIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $eventIds);

        $result = $api->query($soql);

        $events = $result->getRecords();

        foreach($events as $event) {
            $this->results[$event["Id"]] = $event;
        }
        
    }


    public function getSnippets()
    {
        $desc = array_map(function($event) {
            $agenda = $event["Agenda__c"] ?? " ";
            $overview = $event["Overview__c"] ?? " ";
            $banner = $event["Banner_Location_Text__c"] ?? " ";
            $venue = $event["Venue__c"] ?? " ";
            $description = $agenda . $overview . $banner . $venue;

            $description = strip_tags($description, "<br>");

            $description = str_replace("<br>", " ",$description);

            return $description;
        }, $this->results);

        $this->documents = $desc;

        $this->buildSnippets();

    }



    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["Name"];
        //$snippet    = substr(strip_tags($result["ClickpdxCatalog__HtmlDescription__c"]),0,255);

        $snippet    = array_shift($this->snippets);
        $snippet    = str_replace('&nbsp;', ' ', $snippet);

        $domain     = "https://ocdla.force.com";
        $result     = new SearchResult($title,$snippet,"{$domain}/OcdlaProduct?id={$docId}");
        $result->setTemplate("event");

        return $result;
    }
       
}