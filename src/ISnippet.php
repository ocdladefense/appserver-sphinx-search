<?php


interface ISnippet {


    public function getDocumentIds();


    public function loadDocuments($productIds);

    public function getSnippets();


    public function newResult($docId);

    


}