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

    private static $query = "SELECT Id, ResourceId__c, Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c from Media__c WHERE Id IN (%s)";


    private $template = "video";



    public function __construct()
    {
        $this->index = "ocdla_videos";
    }



    public function getDocumentIds() {

        $filter = function($match) {
            $index = $match["indexname"];
            return "ocdla_videos" == $index;
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


    // This function should be called loadSnippets, not getSnippets.
    public function loadSnippets() {

    }

    public function getSnippets()
    {
        $previews = array_map(function($video) {
            $html = $video["Name"];
            $standard = $video["Name"];

            $html = utf8_decode($html);
            $html = preg_replace('/\x{00A0}+/mis', " ", $html);
            
            return empty($html) ? $standard : $html;
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
        $result     = new SearchResult($title,$snippet,"{$domain}/Videos?id={$docId}");
        $result->setTemplate("video");

        return $result;
    }
       
}