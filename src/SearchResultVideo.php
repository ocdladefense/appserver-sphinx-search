<?php
/**
 * @class SearchResultVideo
 * 
 * Represents a result set of Media and includes methods
 * to retrieve the original Media names and descriptions for rendering
 * the actual search results to the user.
 */
use Mysql\DbHelper;


class SearchResultVideo extends SearchResultSet implements ISnippet {

    private static $query = "SELECT Id, ResourceId__c, Name, Event__c, Event__r.Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c from Media__c WHERE Id IN (%s)";


    private $template = "video";

    public function __construct()
    {
        $this->index = "ocdla_videos";
    }



    public function getDocumentIds() {

        foreach(self::$matches as $id => $match) {
            $index = $match["indexname"];
            $altId = $match["alt_id"];
            if("ocdla_videos" == $index) $this->results[$altId] = $match;
        }


        // Returns DbSelectResult
        return array_keys($this->results);
    }


    

    public function loadDocuments($videoIds)
    {
        $api = loadApi();


        $soql = DbHelper::parseArray(self::$query, $videoIds);

        $result = $api->query($soql);

        $videos = $result->getRecords();

        foreach($videos as $video) {
            $this->results[$video["Id"]] = $video;
        }

    }


    public function getSnippets()
    {
        $desc = array_map(function($video) {
            $html = $video["Name"];
            $standard = $video["Name"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
        }, $this->results);

        $this->documents = $desc;

        $this->buildSnippets();

    }



    public function newResult($docId) {
        $result     = $this->results[$docId];       
        $title      = $result["Name"];
        $url        = $result["ResourceId__c"];
        //$snippet    = substr(strip_tags($result["ClickpdxCatalog__HtmlDescription__c"]),0,255);

        $snippet    = array_shift($this->snippets);
        $snippet    = str_replace('&nbsp;', ' ', $snippet);

        $domain     = "https://ocdla.force.com";
        $result     = new SearchResult($title,$snippet,$url);
        $result->setTemplate("video");

        return $result;
    }
       
}